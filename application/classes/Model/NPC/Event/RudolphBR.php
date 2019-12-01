<?php

class Model_NPC_Event_RudolphBR extends Model_NPC_Event_Rudolph
{
    protected static $alcohol_scaling = 0.4;
    protected static $inventory_size = 180;
    protected static $freeze_factor = 1.0;

    protected $num_upgrades = 0;
    protected $upgrade_electro = false;

    protected $upgrade_provide_laser = false;
    protected $upgrade_passive_laser = false;
    protected $upgrade_reload_laser = false;

    protected $upgrade_gyro = false;

    protected $upgrade_thermo = false;
    protected $upgrade_fermo = false;

    protected $upgrade_eye = false;
    protected $upgrade_eye2 = false;


    protected $fermo_target = 0;

    public function thermo_factor(): float {
        if (!$this->upgrade_thermo) return 0.0;
        $d = $this->get_status()->get(Model_Status::MS_STAT_DRUNK);
        if ($d < 50) return 0.1 * ($d/50.0);
        else return 0.1 + 0.25 * (($d-50.0)/50.0);
    }

    public function dyno_factor(): float {
        if (!$this->upgrade_thermo) return 0.0;
        $d = $this->get_status()->get(Model_Status::MS_STAT_DRUNK);
        if ($d < 50) return 0.5 * ($d/50.0);
        else return 0.5 + 1.5 * (($d-50.0)/50.0);
    }

    public function is_fighter() {
        return true;
    }

    public function ai_tumble()
    {
        if (!$this->upgrade_gyro) parent::ai_tumble();
    }

    public function ai()
    {
        parent::ai();
        $fermo = min(5,max(0, $this->fermo_target - $this->get_status()->get(Model_Status::MS_STAT_DRUNK)));

        if ($fermo > 0) {
            $w = max(0,$this->get_status()->get(Model_Status::MS_STAT_THIRST) - 30);
            $h = max(0,$this->get_status()->get(Model_Status::MS_STAT_HUNGER) - 30);
            $fermo = min(min($fermo, $w / 0.1), $h / 0.75 );

            if ($fermo > 0) {
                $this->get_status()->modify(
                    Model_Status::MS_STAT_DRUNK, $fermo,
                    Model_Status::MS_STAT_THIRST, $fermo * -0.1,
                    Model_Status::MS_STAT_HUNGER, $fermo * -0.75);

                $this->am_ferm = true;
            }
        }

        $busy = $this->get_status()->retrieve('passout') || $this->get_status()->retrieve('fragile');
        $d = $this->get_status()->get(Model_Status::MS_STAT_DRUNK);

        if ($this->upgrade_eye && !$busy) {

            if (Tool_Gambling::random( 0.15 - ($d/100.0) )) {
                $extra = Tool_Gambling::random( 0.25 - ($d/10.0) );

                $item = $extra
                    ? $this->location()->item_factory()->spawn(true,false)
                    : $this->location()->item_factory()->spawn();

                if ($item !== null && Tool_System::instance_of($item, Model_Items_Virtual_Invoke_Abstract::cls())) {
                    $item->grind();
                    $item = null;
                }

                if ($item) {
                    Tool_Scripts::place_new_item($item);
                    foreach (Globals::CurrentGameF()->get_initialized_events() as $ev)
                        $ev->event_findItem($this->location(), $item);
                    foreach (Tool_Scripts::at_location($this->location_class(), true, false) as $p)
                        $p->log()->add($extra
                                           ? ':name präsentiert dir stolz einen Gegenstand, den er soeben gefunden hat!'
                                           : ':name präsentiert dir einen (leicht angesabberten) Gegenstand, den er soeben gefunden hat!'
                            , [':name' => $this->name()]);
                }
            }
        }

        if ($this->upgrade_eye2 && !$busy && Tool_Gambling::random(0.05) && !$this->location()->item_factory()->is_empty()) {

            if ($d < 1) {
                $this->location()->item_factory()->replenish(0.4);
                foreach (Tool_Scripts::at_location($this->location_class(), true, false) as $p)
                    $p->log()->add(':name hat Geröll weggeräumt, unter dem sich möglicherweise neue Gegenstände befinden!', [':name' => $this->name()]);
            } elseif ($d >= 50) {
                $this->location()->item_factory()->replenish(-0.4);
                foreach (Tool_Scripts::at_location($this->location_class(), true, false) as $p)
                    $p->log()->add('Auf seiner taumelnden Suche nach neuen Gegenständen ist :name gegen eine instabile Wand gestopert! Dabei wurde ein Teil der Ruine verschüttet, in dem wir jetzt keine Items mehr finden können...', [':name' => $this->name()]);
            } else
                foreach (Tool_Scripts::at_location($this->location_class(), true, false) as $p)
                    $p->log()->add(':name sucht angestrengt nach neuen Gegenständen, hat aber Probleme, sich zu konzentrieren...', [':name' => $this->name()]);
        }


    }

    public function create_combatant() {
        return Model_Combat_Players_Rudolph::create_linked_actor($this, $this->is_drunk() ? 'rudolph_d.jpg' : 'rudolph.jpg', $this->upgrade_provide_laser, $this->upgrade_passive_laser, $this->upgrade_reload_laser);
    }

    public function entity_description() {
        return 'Dieses majestätische Tier ist mit einer leuchtenden roten Nase ausgestattet, die Feinden ordentlich einheizen kann...';
    }

    public function hid(): Model_Hid
    {
        $hid = parent::hid();

        $busy = $this->get_status()->retrieve('passout') || $this->get_status()->retrieve('fragile');

        // Bat+A
        if ($this->upgrade_electro && !$busy)
        {
            $hid
                ->add_action('Stromstoß', Model_Action::factory()
                    ->requirement(Model_Items_Battery::cls(), 1)
                    ->show_as(Model_Effect::factory()->buff(Model_Buffs_Exited::cls(), false, 2)->effect(Model_Status::MS_STAT_DRUNK, 10))
                    ->effect(Model_Effect::factory()
                                 ->custom(function(Model_Player $p) {
                                     if ($this->get_status()->get(Model_Status::MS_STAT_DRUNK) > 90 && !$this->upgrade_gyro) {
                                         $p->log()->add(':name zuckt hoch und steht für einen kurzen Moment kerzengerade - nur um Sekunden später wie ein Wackelpudding hin- und herzuschaukeln. Vielleicht hast du es mit den Elektroschocks etwas übertrieben?', [':name' => $this->name()]);
                                         new Model_Buffs_Drunk2($this,8);
                                     } else {
                                         $p->log()->add('So ein kleiner Stromstoß mitten ins zentrale Nervensystem kann sicherlich nicht schaden... oder?');
                                         new Model_Buffs_Exited($this,2);
                                     }

                                     $this->get_status()->modify(Model_Status::MS_STAT_DRUNK, 10);
                                     $this->set_am_stat();
                                     $this->am_electro = true;
                                 })
                    )
                )->add_action('Starker Stromstoß', Model_Action::factory()
                    ->requirement(Model_Items_Generic_Supercharger::cls(), 1)
                    ->show_as(Model_Effect::factory()->buff(Model_Buffs_Exited::cls(), false, 10)->effect(Model_Status::MS_STAT_DRUNK, 100))
                    ->effect(Model_Effect::factory()
                                 ->custom(function(Model_Player $p) {
                                     if ($this->get_status()->get(Model_Status::MS_STAT_DRUNK) > 10 && !$this->upgrade_gyro) {
                                         $p->log()->add(':name zuckt hoch und steht für einen kurzen Moment kerzengerade - nur um Sekunden später wie ein Wackelpudding hin- und herzuschaukeln. Vielleicht hast du es mit den Elektroschocks etwas übertrieben?', [':name' => $this->name()]);
                                         new Model_Buffs_Drunk2($this, $this->get_status()->get(Model_Status::MS_STAT_DRUNK) > 50 ? 16 : 8);
                                     } else {
                                         $p->log()->add('So ein kleiner Stromstoß mitten ins zentrale Nervensystem kann sicherlich nicht schaden... oder?');
                                         new Model_Buffs_Exited($this,10);
                                     }

                                     $this->get_status()->modify(Model_Status::MS_STAT_DRUNK, 100);
                                     $this->set_am_stat();
                                     $this->am_electro = true;
                                 })
                    )
                );
        }


        //Ferm+A
        if ($this->upgrade_fermo)
        {
            if ($this->fermo_target != 0)
                $hid
                    ->add_action('Fermentor ausschalten', Model_Action::factory()
                        ->effect(Model_Effect::factory()
                                     ->custom(function(Model_Player $p) {
                                         $this->fermo_target = 0;
                                         $p->log()->add('Du hast den Fermentor von :name ausgeschaltet.', [':name' => $this->name()]);
                                     })
                        )
                    );
            if ($this->fermo_target != 55)
                $hid
                    ->add_action('Fermentor auf mittlere Stufe stellen', Model_Action::factory()
                        ->effect(Model_Effect::factory()
                                     ->custom(function(Model_Player $p) {
                                         $this->fermo_target = 55;
                                         $p->log()->add('Du hast den Fermentor von :name auf die mittlere Stufe eingestellt.', [':name' => $this->name()]);
                                     })
                        )
                    );
            if ($this->fermo_target != 90)
                $hid
                    ->add_action('Fermentor auf höchste Stufe stellen', Model_Action::factory()
                        ->effect(Model_Effect::factory()
                                     ->custom(function(Model_Player $p) {
                                         $this->fermo_target = 90;
                                         $p->log()->add('Du hast den Fermentor von :name auf die höchste Stufe eingestellt.', [':name' => $this->name()]);
                                     })
                        )
                    );
        }


        return $hid;
    }

    public function install_upgrade_getup_counter(): int {
        return $this->num_upgrades++;
    }

    public function set_upgrade_electro(bool $b) { $this->upgrade_electro = $b; }

    public function set_upgrade_provide_laser(bool $b) { $this->upgrade_provide_laser = $b; }
    public function set_upgrade_passive_laser(bool $b) { $this->upgrade_passive_laser = $b; }
    public function set_upgrade_reload_laser(bool $b)  { $this->upgrade_reload_laser = $b; }

    public function set_upgrade_gyro(bool $b) { $this->upgrade_gyro = $b;  }

    public function set_upgrade_thermo(bool $b) { $this->upgrade_thermo = $b;  }
    public function set_upgrade_fermo(bool $b) { $this->upgrade_fermo = $b;  }

    public function set_upgrade_eye(bool $b) { $this->upgrade_eye = $b;  }
    public function set_upgrade_eye2(bool $b) { $this->upgrade_eye2 = $b;  }
}