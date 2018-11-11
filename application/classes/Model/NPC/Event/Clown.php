<?php

class Model_NPC_Event_Clown extends Model_NPC_Humanoid
{
    protected static $buffs_metabolism_name = 'Model_Buffs_Event_Fakemetabolism';
    protected static $buffs_place_various = false;
    protected static $death_is_enemy = true;

    protected static $abillities = [];

    protected $is_aggresive = false;
    protected $last_move = 0;

    public function __construct($name = null) {
        parent::__construct('Gruseliger Clown');

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH,  20,
            Model_Status::MS_STAT_ENERGY, 100,
            Model_Status::MS_STAT_HUNGER,  95,
            Model_Status::MS_STAT_THIRST,  95,
            Model_Status::MS_STAT_SLEEPY, 100
        );

        $this->inventory()->add(new Model_Items_Knife);
        $this->inventory()->add(new Model_Items_Clownmask);
    }

    public function entity_action() {
        return 'Unbekannt';
    }

    public function entity_description() {
        return 'Ein gruseliger, blutbeschmierter Clown... er scheint nicht auf dich zu reagieren. Befindet sich ein Mensch unter der Maske, oder ein Zombie?';
    }

    public function is_fighter() {
        return $this->is_aggresive;
    }

    public function create_combatant() {
        return Model_Combat_Event_Clown::create_linked_actor($this);
    }

    protected function generate_dead_body() {
        $b = new Model_Items_Body('Gruseliger Clown', 'Wirklich lustig war dieser Clown nicht...');
        return $b;
    }

    public function kill() {
        if ($this->location() && !$this->is_aggresive) $this->location()->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, $this->id(), true));
        else parent::kill();
    }

    protected function handle_death() {
        return true;
    }

    private function attack() {
        $this->is_aggresive = true;

        $cpl1 = []; $cpl2 = [];
        foreach (Tool_Scripts::at_location($this->location_class()) as $npl)
            if (Tool_System::instance_of($npl, Model_NPC_Event_Clown::cls()))
                $cpl2[] = $npl;
            else if ($npl->is_fighter()) $cpl1[] = $npl;

        if (count($cpl1) > 0 && count($cpl2) > 0) Tool_Scripts::combat([$cpl1,$cpl2],false,2,$this->location(),'Der Clown greift an!');
        $this->is_aggresive = false;
    }

    public function tick() {
        $this->last_move++;
        $this->get_status()->modify(Model_Status::MS_STAT_ENERGY, 1.5);
        return parent::tick();
    }

    public function ai() {
        $busy = $this->get_status()->retrieve('passout') || $this->get_status()->retrieve('fragile');

        if (!count(Tool_Scripts::at_location($this->location_class(), true, false)) && Tool_Gambling::random(0.12)) {
            $this->attack();
            return;
        }

        if (!$busy && $this->last_move >= 12) {

            $dest = [];
            foreach (Globals::CurrentGameF()->mapF($this->location_class())->get_adjacent_regions($this->location_class()) as $lid) {
                $l = Globals::CurrentGameF()->location($lid);
                if (!$l || Tool_System::instance_of($l, Model_Places_Abstract_Hideout::cls()) || $this->get_status()->get(Model_Status::MS_STAT_ENERGY) < Globals::CurrentGameF()->mapF($this->location_class())->get_distance($this->location_class(), $lid)) continue;
                    $dest[] = $l;
            }

            /** @var Model_Places_Abstract_Place $target */
            $target = Tool_Gambling::select($dest);

            if ($target) {
                Controller_Map::code_go(false, $target->uin(), true, false, []);
                $this->last_move = 0;
            }

        }
    }

    public function hid() {
        return parent::hid()
            ->add_action('Näher kommen', Model_Action::factory()
                ->show_as(Model_Effect::factory()
                    ->ambiguous_effect()
                )
                ->effect(Model_Effect::factory()
                    ->custom(function($p) {
                        /** @var Model_Player $p */
                        $p->log()->add('Als du dich langsam dem Clown näherst, zieht er ein Messer und greift an!');
                        $this->attack();
                    })
                )
            )
            ;
    }
}