<?php

class Model_NPC_Event_Rudolph extends Model_NPC_Animal
{
    public const RDLPH_EVENT_STAT_UNDEFINED = 0;
    public const RDLPH_EVENT_STAT_SOBER = 1;
    public const RDLPH_EVENT_STAT_TIPSY = 2;
    public const RDLPH_EVENT_STAT_DRUNK = 3;

    protected static $escort_functions = [Interface_Plentity::IC_ALLOW_ANY];

    protected static $alcohol_scaling = 0.33333;
    protected static $inventory_size = 250;
    protected static $comfort_threshold = 55;

    protected $am_stat_before = Model_NPC_Event_Rudolph::RDLPH_EVENT_STAT_UNDEFINED;
    protected $am_stat_latest = Model_NPC_Event_Rudolph::RDLPH_EVENT_STAT_UNDEFINED;
    protected $am_strong = false;
    protected $am_auto = false;
    protected $am_item = true;

    protected $light = false;
    
    protected static $abillities = [
        Interface_Plentity::IC_TRIGGER_ITEM_TICKS,
        Interface_Plentity::IC_TRIGGER_LOCATION_TICKS,
        Interface_Plentity::IC_TRIGGER_ITEM_FINDINGS
    ];

    public function __construct() {
        parent::__construct('Rudolph');

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, 100,
            Model_Status::MS_STAT_ENERGY, 100,
            Model_Status::MS_STAT_HUNGER, random_int(40,75),
            Model_Status::MS_STAT_THIRST, random_int(45,75),
            Model_Status::MS_STAT_SLEEPY, 100
        );

        $this->get_status()->scaling_add(Model_Status::MS_STAT_FREEZE, Model_Status::MS_EFFECT_GLOBAL, 'rudolph_freeze', 0);
    }

    protected function set_am_stat(): void
    {
        $this->am_stat_before = $this->am_stat_latest;

        if ($this->is_drunk()) $this->am_stat_latest = static::RDLPH_EVENT_STAT_DRUNK;
        elseif ($this->get_status()->get(Model_Status::MS_STAT_DRUNK) > 0) $this->am_stat_latest = $this->am_stat_latest = static::RDLPH_EVENT_STAT_TIPSY;
        else $this->am_stat_latest = $this->am_stat_latest = static::RDLPH_EVENT_STAT_SOBER;

        $this->am_strong = false;
        $this->am_auto = false;
        $this->am_item = false;
    }

    public function tick()
    {
        if ($this->light && !$this->is_drunk()) {
            $this->light = false;
            foreach (Tool_Scripts::at_location($this->location_class(), true, false) as $p)
                $p->log()->add(
                    'Das Licht aus der Nase deines Rentiers ist verloschen...'
                );
        }
        return parent::tick();
    }

    protected function auto_drink(): bool
    {
        return
            $this->get_status()->get(Model_Status::MS_STAT_DRUNK) > 00 &&
            $this->get_status()->get(Model_Status::MS_STAT_DRUNK) < 90;
    }

    public function ai() {
        $busy = $this->get_status()->retrieve('passout') || $this->get_status()->retrieve('fragile');

        if (!$busy && $this->is_drunk()) {

            if (Tool_Gambling::random(($this->get_status()->get(Model_Status::MS_STAT_DRUNK) - 40) / 200)) {
                new Model_Buffs_Drunk2($this, random_int(1,5));
                $this->location()->log()->add(
                    'Ohje... :name hat anscheinend das Gleichgewicht verloren.', [':name' => $this->name()]);
            } elseif (Tool_Gambling::random(($this->get_status()->get(Model_Status::MS_STAT_DRUNK) - 40) / 75)) {

                $events = array(':name hat dir gerade auf deine Schuhe gepinkelt...',
                                ':name stimmt ein anzügliches Lied über weibliche Elfen an...',
                                ':name hat dir das gesamte Gesicht abgeleckt...',
                                ':name umarmt gerade einen MyLittlePony Werbeaufsteller...',
                                ":name's Bewegungen erinnern gerade ein bisschen an einen schon sehr vermoderten Zombie...",
                                ':name stellt gerade enttäuscht fest, dass seine Zunge nicht so weit reicht wie die eines Hundes...',
                                ':name verprügelt gerade eine Weihnachtsmann-Statue...',
                                ':name beschwert sich bei einem verdorrten Strauch über sein Leben...'
                );
                $this->location()->log()->add(Tool_Gambling::select($events), [':name' => $this->name()]);
            }
        }

        $auto = false;

        // Item Consumption
        if (!$busy && $this->auto_drink()) {
            $ic = Tool_Npc::get_satisfactory_item($this, true, false, Model_Status::MS_STAT_DRUNK,
                [Model_Status::MS_STAT_DRUNK => [max(0,100 - $this->get_status()->get(Model_Status::MS_STAT_DRUNK)), false]],
                [], false
            );

            if ($ic) {
                /** @var Model_Items_Abstract_Item $item */
                [$item, $action] = $ic;
                Globals::setCurrentPlayer($this);
                Controller_Act::code_item($item->uin(), $action);
                Globals::restorePrimaryPlayer();
                $auto = true;
            }
        }

        parent::ai();
        $this->set_am_stat();
        $this->am_auto = $auto;
    }

    public function dispense_light(): bool
    {
        return $this->is_drunk() && !$this->get_status()->retrieve('fragile') && $this->light;
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

    public function hid(): Model_Hid
    {
        $hid = parent::hid();
        $selection = [];

        if (($this->am_stat_latest !== $this->am_stat_before) || $this->am_strong || $this->am_auto || $this->am_item) {

            if ($this->am_auto) {
                if ($this->am_stat_latest === static::RDLPH_EVENT_STAT_TIPSY) $selection = ['Ich... ähm... bin sicher die Flasche war vorher schon leer!', 'Verflucht, schon leer...'];
                if ($this->am_stat_latest === static::RDLPH_EVENT_STAT_DRUNK) $selection = ['Guckmal... hihi... dassss habich mit eiiiiiinem Zug leer getrunken.'];
            } else {

                if ($this->am_stat_before === static::RDLPH_EVENT_STAT_SOBER) {
                    if     ($this->am_stat_latest === static::RDLPH_EVENT_STAT_TIPSY) $selection = $this->am_strong ? ['*hust* OH... oh, ich glaube jetzt ist mir ein bisschen schwindelig...']
                                                                                                                    : ['Eigentlich... wollte ich ja mit dem Trinken aufhören. Naja, ein Schluck schadet sicher nicht.'];
                    elseif ($this->am_stat_latest === static::RDLPH_EVENT_STAT_DRUNK)                    $selection = ['*hust* *hust* Ohohoh... hey.... siess... siessu auch die Sterne...?'];
                }

                if ($this->am_stat_before === static::RDLPH_EVENT_STAT_TIPSY) {
                    if      ($this->am_stat_latest === static::RDLPH_EVENT_STAT_SOBER) $selection = ['Au... aua, mein armer Kopf... Musste das denn sein, mich so abzufüllen?',
                                                                                                     'Oh nein... ich wollte dieses Weihnachten doch nüchtern bleiben...'];
                    elseif  ($this->am_stat_latest === static::RDLPH_EVENT_STAT_TIPSY) $selection = $this->am_strong ? ['WOW... das brennt die Kehle frei!']
                                                                                                                     : ['Hey, danke. Wenigstens sitze ich hier nicht auf dem Trockenen.'];
                    elseif  ($this->am_stat_latest === static::RDLPH_EVENT_STAT_DRUNK) $selection = $this->am_strong ? ["Ohja... das... dassss ... *hicks* ... eh... wo warnnir... steh'n gebliem?"]
                                                                                                                     : ['Heeeey.... lansam... merkich was...'];
                }

                if  ($this->am_stat_before === static::RDLPH_EVENT_STAT_DRUNK) {
                    if      ($this->am_stat_latest === static::RDLPH_EVENT_STAT_DRUNK) $selection = ["Sachma... *hicks* ... wennichs nich besser wüsse... würd ich sag'n... dassu mich hier abfüllst... *hicks*"];
                    elseif  ($this->am_stat_latest === static::RDLPH_EVENT_STAT_TIPSY) $selection = ['Hey... HEY BARKEEPER! Was soll das, ich nüchtere ja aus!'];
                }
            }
        }

        if (empty($selection)) {
            $dialog_sober = ["Hey, wie geht's?", 'Schön dich zu sehen.',
                             'Wir sollten dort drüben mal nachsehen.',
                             'Endlich habe ich mal ein bisschen Gesellschaft!',
                             'Ist dir nicht kalt?',
                             'Soll ich dir beim Tragen helfen?'];
            $dialog_tipsy = ['Man, hab ich einen Durst...', "Gibt's noch was zu trinken?",
                             'Trinkst du das noch?',
                             'Komm schon, lass uns zum Glühweinstand gehen!',
                             'Wie wärs, wenn ich die Getränke trage?',
                             'Angetrunken? Ich? Quatsch...',
                             'Keine Angst, ich kann noch fahren...'];
            $dialog_drunk = ['Heheee.... deine Naaase is komisch...',
                             'Warsu vorhin auch schon su dritt?',
                             'Ha... hassu das auch gehört?',
                             'Binnoch ... totaaaal nü... nü.... nüch betrunken!'];
            $dialog_tumbling = ['Seiwann hab ichn .. Gummibeine... ?',
                                'Kannsu mal ds Karussell.. ausmachen?',
                                'Uuuuuuuh...... alles dreeeeeeeeht sich ...',
                                'Kannich... mich mal kurss... bei dir anlehnen?',
                                'Der Booooooooden wackelt...',
                                'Wieso... kannsu mit swai Beinen... besssser stehn als wie ich... mi vieeeer...?'];
            $dialog_passout = ['Baaaaaaaaaaaaaaaah.......', '* hicks *',
                               'Uuuuuuuuuuuh.......'];

            if (Tool_System::instance_of($this->location(), Model_Places_Northpole_House_Secondfloor::cls())) {
                $dialog_sober[] = 'Der Boden hier ist ganz schön wackelig... Ich glaube, wenn ich nicht vorsichtig bin, breche ich hier durch.';
                $dialog_tipsy[] = 'Kommt mir das nur so vor, oder ist der Boden hier ganz schön wackelig...?';
                $dialog_drunk[] = 'Irrenwie... isses grad ganschön schwer... *hicks* ... graaade scho stehn...';
            }

            if ($this->get_status()->get(Model_Status::MS_STAT_HEALTH) > 50) {
                $dialog_sober = array_merge($dialog_sober, ['Könntest du mich mal am Rücken kratzen?',
                                                            'Ich fühl mich super!',
                                                            'Alles bestens, danke der Nachfrage!']);
                $dialog_tipsy = array_merge($dialog_tipsy, ['Mein Kopf kribbelt...',
                                                            'Ich fühl mich leicht...']);
                $dialog_drunk = array_merge($dialog_drunk, ['Ich glaub ... einen könnt ich noch ...',
                                                            'Wusses du, dasss mein Geweih n suuper Arschkratzer is?']);
                $dialog_tumbling = array_merge($dialog_tumbling, ['♫ Schneeflöckchen ... ♪ geiles Röckchen ... ♬']);
            } else {
                $dialog_sober = array_merge($dialog_sober, ['Ich fühl mich nicht besonders...',
                                                            'Autsch... Mach dir keine Sorgen, dass wird sicher wieder...']);
                $dialog_tipsy = array_merge($dialog_tipsy, ['Uuuh... ich kann mich nicht konzentrieren...',
                                                            'Aah... das betäubt den Schmerz.']);
                $dialog_drunk = array_merge($dialog_drunk, ['Nie... NIE hab isch... Geschenke gekriegt. Aber immer muss... mussich mich für den allen Sack ab.. abrackern!',
                                                            'Uuuh..... bin su aaalt für solche Partys...']);
                $dialog_tumbling = array_merge($dialog_tumbling, ['Urgh... muss... gleich... ko... kotzen...']);
            }

            if (Globals::hasCurrentPlayer() && Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_STAT_DRUNK) >= 50) {
                $dialog_sober = array_merge($dialog_sober, ['Oh je... du bist betrunken, nicht wahr?',
                                                            'Ist.. alles OK mit dir?',
                                                            'Ähm... willst du dich vielleicht bei mir anlehnen?']);
                $dialog_tipsy = array_merge($dialog_tipsy, ['In Gesellschaft trinkt es sich einfach schöner!',
                                                            'Aber lass mir was übrig, ok?']);
                $dialog_drunk = array_merge($dialog_drunk, ['Hehe... du bisss besoffen... *hicks*',
                                                            'Heyeyyy... nimmsu... nimmsu die Hand da weg!!!']);
            }

            if ($fragile = $this->get_status()->retrieve('fragile')) {
                if     (Tool_System::instance_of($fragile, 'Model_Buffs_Drunk')) $selection  = $dialog_passout;
                elseif (Tool_System::instance_of($fragile, 'Model_Buffs_Drunk2')) $selection = $dialog_tumbling;
            }
            elseif ($this->is_drunk()) $selection = $dialog_drunk;
            elseif ($this->get_status()->get(Model_Status::MS_STAT_DRUNK) > 0) $selection = $dialog_tipsy;
            else $selection = $dialog_sober;
        }

        if (empty($selection)) $selection = ['...'];

        $hid->add_action('Ansprechen', Model_Action::factory()->effect(Model_Effect::factory()->custom(function() {$this->set_am_stat();})->message(Tool_Gambling::select($selection))));

        if (!$this->dispense_light())
            $hid->add_action('Nasale Beleuchtung aktivieren', Model_Action::factory()
                ->condition(function() {
                    if (!$this->is_drunk()) return 'sober';
                    if (($fragile = $this->get_status()->retrieve('fragile')) && Tool_System::instance_of($fragile, 'Model_Buffs_Drunk')) return 'drunk';
                    if (($fragile = $this->get_status()->retrieve('fragile')) && Tool_System::instance_of($fragile, 'Model_Buffs_Drunk2')) return 'drunk2';
                    return true;
                })
                ->fail_message(
                    'Jeder weis doch, dass man für eine leuchtend rote Nase ordentlich Alkohol intus haben muss. Du musst dein Rentier also schon noch ein bisschen betanken...', 'sober')
                ->fail_message(
                    'Das war wohl zu viel des Guten... dein Rentier liegt lallend am Boden. Auf die Beleuchtung musst du wohl für eine Weile verzichten...', 'drunk')
                ->fail_message(
                    'Das war wohl zu viel des Guten... dein Rentier kann sich kaum auf den Beinen halten. Auf die Beleuchtung musst du wohl für eine Weile verzichten...', 'drunk2')
                ->effect(Model_Effect::factory()
                    ->message(
                        'Wunderbar, im Schein der roten Nase lässt es sich gleich viel besser nach Items suchen!'
                    )
                    ->custom(function() {$this->light = true;})
                )
            );

        return $hid;
    }

    public function icon() {
        return $this->dispense_light() ? 'reindeer_on.gif' : 'reindeer.gif';
    }

    public function item_preaction(Model_Items_Abstract_Item $item,$action) {
        $this->set_am_stat();
        $set = $item->simple_effects($this,false);
        if (isset($set[$action][Model_Status::MS_STAT_DRUNK]))
            $this->am_strong = ($set[$action][Model_Status::MS_STAT_DRUNK] >= 30);
    }

    public function item_reaction() {
        $strong = $this->am_strong;
        $this->set_am_stat();
        $this->am_strong = $strong;
        $this->am_item = true;
    }
}