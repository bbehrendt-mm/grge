<?php

class Model_NPC_Event_Rudolph extends Model_NPC_Animal
{
    protected static $escort_functions = [Interface_Plentity::IC_ALLOW_ANY];

    protected $last_hideout = null;

    protected static $movement_scaling = 1;
    protected static $alcohol_scaling = 0.33333;
    protected static $inventory_size = 250;
    protected static $comfort_threshold = 50;

    protected $light = false;
    
    protected static $abillities = [
        Interface_Plentity::IC_TRIGGER_ITEM_TICKS,
        Interface_Plentity::IC_TRIGGER_LOCATION_TICKS,
        Interface_Plentity::IC_TRIGGER_ITEM_FINDINGS
    ];

    public function __construct() {
        parent::__construct("Rudolph");

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, 100,
            Model_Status::MS_STAT_ENERGY, 100,
            Model_Status::MS_STAT_HUNGER, mt_rand(40,75),
            Model_Status::MS_STAT_THIRST, mt_rand(45,75),
            Model_Status::MS_STAT_SLEEPY, 100
        );

        $this->get_status()->scaling_add(Model_Status::MS_STAT_FREEZE, Model_Status::MS_EFFECT_GLOBAL, 'rudolph_freeze', 0);
    }

    public function tick()
    {
        if (!$this->is_drunk() && $this->light) {
            $this->light = false;
            foreach (Tool_Scripts::at_location($this->location_class(), true, false) as $p)
                $p->log()->add("Das Licht aus der Nase deines Rentiers ist verloschen...");
        }
        return parent::tick();
    }

    public function ai() {
        $busy = $this->get_status()->retrieve('passout') || $this->get_status()->retrieve('fragile');

        if (!$busy && $this->is_drunk()) {

            if (Tool_Gambling::random(($this->get_status()->get(Model_Status::MS_STAT_DRUNK) - 40) / 200)) {
                new Model_Buffs_Drunk2($this, mt_rand(1,5));
                $this->location()->log()->add("Ohje... :name hat anscheinend das Gleichgewicht verloren.", [':name' => $this->name()]);
            } elseif (Tool_Gambling::random(($this->get_status()->get(Model_Status::MS_STAT_DRUNK) - 40) / 75)) {

                $events = [":name hat dir gerade auf deine Schuhe gepinkelt...", ":name stimmt ein anzügliches Lied über weibliche Elfen an...",
                    ":name hat dir das gesamte Gesicht abgeleckt...", ":name umarmt gerade einen MyLittlePony Werbeaufsteller...",
                    ":name's Bewegungen erinnern gerade ein bisschen an einen schon sehr vermoderten Zombie...",
                    ":name stellt gerade enttäuscht fest, dass seine Zunge nicht so weit reicht wie die eines Hundes...",
                    ":name verprügelt gerade eine Weihnachtsmann-Statue...", ":name beschwert sich bei einem verdorrten Strauch über sein Leben..."
                ];
                $this->location()->log()->add(Tool_Gambling::select($events), [':name' => $this->name()]);

            }


        }

        // Item Consumption
        if (!$busy && $this->get_status()->get(Model_Status::MS_STAT_DRUNK) > 0)
            if ($this->get_status()->get(Model_Status::MS_STAT_DRUNK) < 90) {

                $ic = Tool_Npc::get_satisfactory_item($this, true, false, Model_Status::MS_STAT_DRUNK,
                    [Model_Status::MS_STAT_DRUNK => [max(0,100 - $this->get_status()->get(Model_Status::MS_STAT_DRUNK)), false]],
                    [], false
                );

                if ($ic) {
                    /** @var Model_Items_Abstract_Item $item */
                    list($item, $action) = $ic;
                    Controller_Game::delegate($this, function() use ($item, $action) {
                        Controller_Act::code_item($item->uin(), $action);
                    });
                }
            }

        parent::ai();
    }

    public function dispense_light() {
        return $this->is_drunk() && !$this->get_status()->retrieve('fragile') && $this->light;
    }

    protected function generate_zombified_body() {
        return null;
    }

    public function is_fighter() {
        return false;
    }

    public function entity_species() {
        return 'Rentier';
    }

    public function entity_description() {
        return 'Dieses majestätische Tier ist mit einer leuchtenden roten Nase ausgestattet, die beim Suchen nach items sicherlich sehr hilfreich ist.';
    }

    public function hid() {
        $hid = parent::hid();

        $dialog_sober = ["Hey, wie geht's?", "Schön dich zu sehen.", "Wir sollten dort drüben mal nachsehen.",
            "Endlich habe ich mal ein bisschen Gesellschaft!","Ist dir nicht kalt?","Soll ich dir beim Tragen helfen?"];
        $dialog_tipsy = ["Man, hab ich einen Durst...", "Gibt's noch was zu trinken?", "Trinkst du das noch?",
            "Komm schon, lass uns zum Glühweinstand gehen!","Wie wärs, wenn ich die Getränke trage?",
            "Angetrunken? Ich? Quatsch...", "Verflucht, schon leer..."];
        $dialog_drunk = ["Heheee.... deine Naaase is komisch...", "Warsu vorhin auch schon su dritt?",
            "Ha... hassu das auch gehört?",
            "Binnoch ... totaaaal nü... nü.... nüch betrunken!"];
        $dialog_tumbling = ["Seiwann hab ichn .. Gummibeine... ?", "Kannsu mal ds Karussell.. ausmachen?",
            "Uuuuuuuh...... alles dreeeeeeeeht sich ...", "Kannich... mich mal kurss... bei dir anlehnen?",
            "Der Booooooooden wackelt...", "Wieso... kannsu mit swai Beinen... besssser stehn als wie ich... mi vieeeer...?"];
        $dialog_passout = ["Baaaaaaaaaaaaaaaah.......", "* hicks *", "Uuuuuuuuuuuh......."];

        if ($this->get_status()->get(Model_Status::MS_STAT_HEALTH) > 50) {
            $dialog_sober = array_merge($dialog_sober, ["Könntest du mich mal am Rücken kratzen?", "Ich fühl mich super!", "Alles bestens, danke der Nachfrage!"]);
            $dialog_tipsy = array_merge($dialog_tipsy, ["Mein Kopf kribbelt...", "Ich fühl mich leicht..."]);
            $dialog_drunk = array_merge($dialog_drunk, ["Ich glaub ... einen könnt ich noch ...", "Wusses du, dasss mein Geweih n suuper Arschkratzer is?"]);
            $dialog_tumbling = array_merge($dialog_tumbling, ["♫ Schneeflöckchen ... ♪ geiles Röckchen ... ♬"]);
        } else {
            $dialog_sober = array_merge($dialog_sober, ["Ich fühl mich nicht besonders...", "Autsch... Mach dir keine Sorgen, dass wird sicher wieder..."]);
            $dialog_tipsy = array_merge($dialog_tipsy, ["Uuuh... ich kann mich nicht konzentrieren...", "Aah... das betäubt den Schmerz."]);
            $dialog_drunk = array_merge($dialog_drunk, ["Nie... NIE hab isch... Geschenke gekriegt. Aber immer muss... mussich mich für den allen Sack ab.. abrackern!", "Uuuh..... bin su aaalt für solche Partys..."]);
            $dialog_tumbling = array_merge($dialog_tumbling, ["Urgh... muss... gleich... ko... kotzen..."]);
        }

        $selection = ["..."];
        if (($fragile = $this->get_status()->retrieve('fragile')) && Tool_System::instance_of($fragile, 'Model_Buffs_Drunk')) $selection = $dialog_passout;
        elseif (($fragile = $this->get_status()->retrieve('fragile')) && Tool_System::instance_of($fragile, 'Model_Buffs_Drunk2')) $selection = $dialog_tumbling;
        elseif ($this->is_drunk()) $selection = $dialog_drunk;
        elseif ($this->get_status()->get(Model_Status::MS_STAT_DRUNK) > 0) $selection = $dialog_tipsy;
        else $selection = $dialog_sober;

        $hid->add_action('Ansprechen', Model_Action::factory()->effect(Model_Effect::factory()->message(Tool_Gambling::select($selection))));

        if (!$this->dispense_light())
            $hid->add_action('Nasale Beleuchtung aktivieren', Model_Action::factory()
                ->condition(function() {
                    if (!$this->is_drunk()) return 'sober';
                    if (($fragile = $this->get_status()->retrieve('fragile')) && Tool_System::instance_of($fragile, 'Model_Buffs_Drunk')) return 'drunk';
                    if (($fragile = $this->get_status()->retrieve('fragile')) && Tool_System::instance_of($fragile, 'Model_Buffs_Drunk2')) return 'drunk2';
                    return true;
                })
                ->fail_message("Jeder weis doch, dass man für eine leuchtend rote Nase ordentlich Alkohol intus haben muss. Du musst dein Rentier also schon noch ein bisschen betanken...", 'sober')
                ->fail_message("Das war wohl zu viel des Guten... dein Rentier liegt lallend am Boden. Auf die Beleuchtung musst du wohl für eine Weile verzichten...", 'drunk')
                ->fail_message("Das war wohl zu viel des Guten... dein Rentier kann sich kaum auf den Beinen halten. Auf die Beleuchtung musst du wohl für eine Weile verzichten...", 'drunk2')
                ->effect(Model_Effect::factory()
                    ->message("Wunderbar, im Schein der roten Nase lässt es sich gleich viel besser nach Items suchen!")
                    ->custom(function() {$this->light = true;})
                )
            );

        return $hid;
    }

    public function icon() {
        return $this->dispense_light() ? 'reindeer_on.gif' : 'reindeer.gif';
    }
}