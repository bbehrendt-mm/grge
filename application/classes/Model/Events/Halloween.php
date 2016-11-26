<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Events_Halloween extends Model_Events_Event {

    protected static $event_key = 'halloween';
    protected static $event_name = 'Halloween-Event';

    private $maps = [];
    private $horror_list = [];
    private $npc_list = [];
    private $item_list = [];
    private $clowns = 0;

    public function register_event_map($map) {
        $this->maps[] = $map;
    }

    private function spawn_scarecrow(Model_Places_Abstract_Place $place) {
        /** @global Model_Game $game */
        global $game;

        $scarecrow = new Model_NPC_Event_Scarecrow();
        $scarecrow->location_class($place->uin());
        $game->add_npc($scarecrow);
        $place->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $scarecrow->id(), true));
        $this->npc_list[] = $scarecrow->id();
    }

    private function spawn_clown(Model_Places_Abstract_Place $place) {
        /** @global Model_Game $game */
        global $game;

        if (Tool_System::instance_of($place, Model_Places_Abstract_Hideout::cls()))
            return;

        $clown = new Model_NPC_Event_Clown();
        $clown->location_class($place->uin());
        $game->add_npc($clown);
        $place->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $clown->id(), true));
        $this->npc_list[] = $clown->id();

        $this->clowns++;
    }

    private function spawn_merchant(Model_Places_Abstract_Place $place) {
        /** @global Model_Game $game */
        global $game;

        $merchant = new Model_NPC_Event_Merchant();
        $merchant->location_class($place->uin());
        $game->add_npc($merchant);
        $place->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $merchant->id(), true));
        $this->npc_list[] = $merchant->id();

        $i = new Model_Items_Virtual_Location_FfHalloweenStore();
        $place->inventory()->add($i);
        $this->item_list[] = $i->uin();
    }

    protected function clown_balance() {
        /** @global Model_Game $game */
        global $game;

        $num_locations = count($game->map_main()->get_locations());
        $num_clowns_supposed = ceil($num_locations/7.0);

        while ($num_clowns_supposed > $this->clowns) {
            $l = Tool_Gambling::select($game->map_main()->get_locations());
            if ($l) $this->spawn_clown($game->location($l));
        }
    }

    protected function trigger_activation() {
        /** @global Model_Game $game */
        global $game;

        foreach ($game->playable_entities() as $pl) if (!Tool_Scripts::is_npc($pl)) {
            new Model_Buffs_Scarecrow($pl);
            /** @var $pl Model_Player */
            $pl->log()->add(new Model_Log_Types_Event(static::name(),static::get_key(), true, "Ein eiskalter Schauer läuft dir über den Rücken..."));
        }

        foreach ($game->maps() as $map)
            foreach ($map->get_locations() as $lid) {

                if ($game->map_main()->resolve_fixed_id(2) != $lid && ($game->map_main()->resolve_fixed_id(1) == $lid || Tool_Gambling::random(0.2)))
                    $this->spawn_scarecrow($game->location($lid));

                if (Tool_System::instance_of($game->location($lid), Model_Places_Store::cls()))
                    $this->spawn_merchant($game->location($lid));
            }

        $this->clown_balance();

        return true;
    }

    protected function trigger_deactivation() {
        /** @global Model_Game $game */
        global $game;

        foreach ($this->item_list as $iuin) {
            /** @var Model_Items_Abstract_Item $i */
            $i = $game->uin()->get($iuin, Model_Items_Abstract_Item::cls());
            if ($i) $i->grind();
        }

        foreach ($game->playable_entities() as $pl) {
            $pl->get_status()->remove('scarecrow');
            /** @var $pl Model_Player */
            if (!Tool_Scripts::is_npc($pl))
                $pl->log()->add(new Model_Log_Types_Event(static::name(),static::get_key(), false, "Puuh... das schlimmste scheint vorbei zu sein."));
        }

        foreach ($this->npc_list as $npc) {
            $npc_inst = $game->get_npc($npc);
            if ($npc_inst && $npc_inst->get_status()->alive())
                $npc_inst->kill();
        }

        $d_loc = $game->map_main()->get_by_fixed_id(1);
        if ($d_loc)
            foreach ($this->maps as $map_id) {
                $map = $game->map_by_id($map_id);
                if ($map) {
                    foreach ($map->get_locations() as $subloc)
                        foreach (Tool_Scripts::at_location($subloc) as $p) {
                            $game->location($subloc)->leave($p->id(), Tool_Scripts::is_npc($p) ? Interface_Tickable::IT_TYPE_NPC : Interface_Tickable::IT_TYPE_PLAYER);
                            $p->location_class($d_loc->uin());
                            $d_loc->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $p->id(), Tool_Scripts::is_npc($p)));
                        }
                }
                $game->unregister_map($map_id);
            }

        return true;
    }

    public function tick() {
        return true;
    }

    public function event_playerCreation(Interface_Plentity $entity) {
        if (!Tool_Scripts::is_npc($entity))
            new Model_Buffs_Scarecrow($entity);
    }

    public function event_locationCreation(Model_Places_Abstract_Place $place) {
        $this->clown_balance();

        if (!Tool_System::instance_of($place, Model_Places_Abstract_Hideout::cls()) && Tool_Gambling::random(0.2))
            $this->spawn_scarecrow($place);

        if (Tool_System::instance_of($place, Model_Places_Store::cls()))
            $this->spawn_merchant($place);
    }

    private function handle_soulSpawn(Model_Places_Abstract_Place $place) {
        $soul_chance_table = [
            'Model_Places_Hospital_Er' => 0.35,
            'Model_Places_Mental' => 0.3,
            'Model_Places_House_Hobby' => 0.3,
            'Model_Places_Mausoleum' => 0.1,
            'Model_Places_Asylumhideout' => 0.1,
            'Model_Places_Druglab' => 0.1,
            'Model_Places_Hospital_Morgue' => 0.1,
            'Model_Places_Hospital' => 0.08,
            'Model_Places_Abstract_Hideout' => 0,
            'Model_Places_Abstract_Node' => 0
        ];
        $soul_chance = 0.02;

        foreach ($soul_chance_table as $loc => $cn)
            if (Tool_System::instance_of($place, $loc)) {
                $soul_chance = $cn;
                break;
            }

        if (Tool_Gambling::random($soul_chance)) {
            $soul = Tool_Gambling::random($soul_chance) ? new Model_Items_Soul2(1) : new Model_Items_Soul(1);
            $place->inventory()->add($soul);
            $place->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_SOUL, $soul,-1));
        }
    }

    private function handle_horrorActions(Model_Places_Abstract_Place $place) {
        /** @global Model_Game $game */
        global $game;

        // Cooler closing
        if (Tool_System::instance_of($place, Model_Places_Burgerjoint::cls()) && !in_array($place->uin(), $this->horror_list) && Tool_Gambling::random(0.1)) {
            $this->horror_list[] = $place->uin();
            /** @var Model_Items_Virtual_Location_Cooler[] $vi */
            $vi = $place->inventory()->get('Model_Items_Virtual_Location_Cooler');
            if ($vi && !$vi[0]->remaining_actions('cooler_open')) {
                $vi[0]->remaining_actions('cooler_open_again_2', 1);

                foreach (Tool_Scripts::at_location($place->uin(), true, false) as $pl) {
                    $pl->achievements()->achieve(Model_Achievement::MA_HALLOWEEN_15);
                    $pl->log()->add('Die Tür zur Kühlkammer ist mit einem Knall zugefallen. Komisch, eigentlich warst du dir sicher, sie mit einem Keil gesichert zu haben...');
                }
            }
        }
        // Construction site
        elseif (Tool_System::instance_of($place, Model_Places_Constructionsite::cls()) && Tool_Gambling::random(0.1)) {
            foreach (Tool_Scripts::at_location($place->uin(), true, false) as $pl) {
                $pl->achievements()->achieve(Model_Achievement::MA_HALLOWEEN_15);
                $pl->log()->add('Du willst dich gerade ausruhen, da hörst du wie etwas hinter dir auf den Boden aufschlägt. Es scheint, als wäre eine Leiche von einem Gerüst gefallen!');
            }

            $place->inventory()->add(new Model_Items_Body('Zerfetzte Leiche','Diese Leiche ist ziemlich verstümmelt. Schwer zu sagen, ob das durch den Sturz passiert ist...'));
        }
        // Toilet
        elseif (Tool_System::instance_of($place, Model_Places_Toilet::cls()) && !in_array($place->uin(), $this->horror_list) && Tool_Gambling::random(0.2)) {
            $this->horror_list[] = $place->uin();

            foreach (Tool_Scripts::at_location($place->uin(), true, false) as $pl) {
                $pl->achievements()->achieve(Model_Achievement::MA_HALLOWEEN_15);
                new Model_Buffs_Exited($pl->id(), 6);
                $pl->log()->add('Du bist gerade dabei, den Mülleimer zu durchwühlen, da hörst du wie hinter dir eine Toilettenspülung betätigt wird. Das Geräusch scheint aus der einen Kabine zu kommen, die seit deinem ersten Besuch abgesperrt war...');
            }
        }
        // Insane Asylum
        /** @noinspection PhpUndefinedMethodInspection */
        elseif (Tool_System::instance_of($place, Model_Places_Mental::cls()) && !in_array($place->uin(), $this->horror_list) && $place->get_mental_state() >= 1) {
            $this->horror_list[] = $place->uin();

            $patient = new Model_NPC_Event_Patient();
            $patient->location_class($place->uin());
            $game->add_npc($patient);
            $place->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $patient->id(), true));
            $this->npc_list[] = $patient->id();
        }
    }

    public function event_locationTick(Model_Places_Abstract_Place $place) {
        $this->handle_soulSpawn($place);
        $this->handle_horrorActions($place);
    }

    public function event_generateHIDStack(Model_Items_Abstract_Item &$item, Model_Hid &$hid) {

        // Cooler closing
        if (Tool_System::instance_of($item, Model_Items_Virtual_Location_Cooler::cls())) {
            /** @var $item Model_Items_Virtual_Location_Cooler */
            if ($item->has_action('cooler_open_again_2')) {
                $hid->add_action('Kühlkammer erneut öffnen', Model_Action::factory()
                    ->buttonskin('location')
                    ->description('Die Tür der Kühlkammer muss wohl durch einen Windstoß zugefallen sein - immerhin ist hier ja niemand sonst... oder?')
                    ->requirement(Model_Status::MS_STAT_ENERGY, 5)
                    ->show_as(Model_Effect::factory()
                        ->ambiguous_effect()
                    )
                    ->effect(Model_Effect::factory()
                        ->message('Als du die Tür öffnest, schlägt dir ein beißender Geruch entgegen. Die gesamte Kühlkammer ist plötzlich voll mit verrottendem Fleisch!')
                        ->spawn('Model_Items_Fleshfood',mt_rand(4,10), true)
                    )
                    , 'cooler_open_again_2');
            }
        }
    }

    public function event_executeHIDAction($cls, $name, Model_Action &$action) {
        if (!Tool_Gambling::random(0.08)) return;

        // DILDO EFFECT UPDATE
        if (Tool_System::instance_of($cls, Model_Items_Dildo::cls()) && $name == 'Benutzen')
            $action
                ->decider(function() {return static::$event_key;})
                ->effect(Model_Effect::factory()
                    ->achieve(Model_Achievement::MA_MASOCHIST)
                    ->achieve(Model_Achievement::MA_HALLOWEEN_15)
                    ->spawn('Model_Items_Generic_Cursed', 1, true)
                    ->buff('Model_Buffs_Exited', false, 15)
                    ->message('Als du gerade konzentriert "bei der Arbeit" bist, hörst du plötzlich hinter dir ein Kinderlachen. Du drehst dich erschrocken um, findest hinter dir jedoch nur einen Teddybären...')
                    ,static::$event_key);
        // ALCOHOL EFFECT
        elseif (Tool_System::instance_of($cls, Model_Items_Abstract_Alcohol::cls()) && $name == 'Trinken')
            $action
                ->decider(function() {return static::$event_key;})
                ->effect(
                    Model_Effect::factory()
                        ->consume()
                        ->spawn('Model_Items_Generic_Waterb', 1)
                        ->spawn('Model_Items_Smallbottle')
                        ->achieve(Model_Achievement::MA_HALLOWEEN_15)
                        ->message('Kaum ist der erste Tropfen deine Kehle hinunter gelaufen, merkst du das etwas nicht stimmt. Diese Flasche war mit BLUT gefüllt!!')
                    ,static::$event_key);
    }

    public function event_findItem(Model_Places_Abstract_Place $place, Model_Items_Abstract_Item &$item) {
        if (!Tool_Gambling::random(0.1)) return;

        if (Tool_System::instance_of($place, Model_Places_Abstract_Node::cls()))
            Tool_Scripts::place_new_item(new Model_Items_Generic_Pumpkin());
    }

    public function event_blueprintCreation($config_name, $config_category) {
        /** @global Model_Game $game */
        global $game;

        $massacre_mode = $game && $game->setting_mode(2000);

        $sp_factor = $massacre_mode ? 8 : 1;


        if ($config_name == 'Store' && $config_category == 'items') {

            return Model_Blueprints::factory()
                ->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:brainbox1_0')
                        ->steps(0)
                        ->message('Vielen Dank für Ihren Einkauf! Hier sind ihre GEHIIIIRNE. Bitte beehren Sie uns bald wieder!')
                        ->material([Model_Items_Money::cls() => 1])
                        ->produces([Model_Items_Brainbox::cls() => 1])
                )->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:brainbox2_0')
                        ->steps(0)
                        ->message('Vielen Dank für Ihren Einkauf! Hier sind ihre GEHIIIIRNE. Bitte beehren Sie uns bald wieder!')
                        ->material([Model_Items_Money::cls() => 3])
                        ->produces([Model_Items_Brainbox::cls() => 4])
                )->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:soulcnv_0')
                        ->steps(0)
                        ->material([Model_Items_Soul::cls() => 6])
                        ->produces([Model_Items_Soul2::cls() => 1])
                )->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:soulcn1_0')
                        ->steps(0)
                        ->material([Model_Items_Soul::cls() => $sp_factor * 1])
                        ->produces([Model_Items_Braincoin::cls() => 1])
                )->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:soulcn2_0')
                        ->steps(0)
                        ->material([Model_Items_Soul::cls() => $sp_factor * 5])
                        ->produces([Model_Items_Braincoin::cls() => 5])
                )->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:soulcn3_0')
                        ->steps(0)
                        ->material([Model_Items_Soul::cls() => $sp_factor * 10])
                        ->produces([Model_Items_Braincoin::cls() => 10])
                )->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:soulcn4_0')
                        ->steps(0)
                        ->material([Model_Items_Soul2::cls() => $sp_factor * 1])
                        ->produces([Model_Items_Braincoin::cls() => 5])
                )->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:soulcn5_0')
                        ->steps(0)
                        ->material([Model_Items_Soul2::cls() => $sp_factor * 5])
                        ->produces([Model_Items_Braincoin::cls() => 25])
                )
                ;

        }

        return null;

    }

    public function event_renderHIDAction($cls, $name, Model_Action &$action) {}
}