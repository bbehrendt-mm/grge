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
    ];

    public function __construct() {
        parent::__construct("Rudolph");

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, 100,
            Model_Status::MS_STAT_ENERGY, 100,
            Model_Status::MS_STAT_HUNGER, mt_rand(40,75),
            Model_Status::MS_STAT_THIRST, mt_rand(45,75),
            Model_Status::MS_STAT_SLEEPY, 100,
            Model_Status::MS_STAT_DRUNK, mt_rand(5,10)
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

            if (Tool_Gambling::random(($this->get_status()->get(Model_Status::MS_STAT_DRUNK) - 40) / 200))
                new Model_Buffs_Drunk2($this, mt_rand(1,5));

        }

        // Item Consumption
        if (!$busy && $this->is_drunk())
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