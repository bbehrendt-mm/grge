<?php

class Model_NPC_Event_Patient extends Model_NPC_Humanoid
{
    protected static $buffs_metabolism_name = 'Model_Buffs_Event_Fakemetabolism';
    protected static $buffs_place_various = false;
    protected static $death_is_enemy = true;

    protected static $abillities = [];

    protected $is_aggresive = false;
    protected $is_passified = false;

    public function __construct($name = null) {
        parent::__construct('Verstörter Patient');

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, 30,
            Model_Status::MS_STAT_ENERGY, 66,
            Model_Status::MS_STAT_HUNGER,  6,
            Model_Status::MS_STAT_THIRST,  6,
            Model_Status::MS_STAT_SLEEPY, 66
        );

        $this->inventory()->add(new Model_Items_Hacksaw);
        $num = mt_rand(5,20);
        for ($i = 0; $i < $num; $i++)
            $this->inventory()->add(new Model_Items_Fleshfood);
    }

    public function entity_action() {
        return 'Schleicht umher';
    }

    public function entity_description() {
        return 'Er sieht wie ein Patient dieser Einrichtung aus. Sein Hemd ist voller Blut, und er umklammert irgend etwas mit beiden Händen während er mit leerem Blick die Gänge der Anstalt schleicht. Auf Zurufe reagiert er nicht... anscheinend nimmt er dich nicht einmal wahr. Was er da wohl dabei hat... du könntest versuchen, es ihm wegzunehmen. Immerhin sieht er nicht sehr wehrhaft aus.';
    }

    public function is_fighter() {
        return $this->is_aggresive && !$this->is_passified;
    }

    public function create_combatant() {
        return Model_Combat_Event_Patient::create_linked_actor($this);
    }

    protected function generate_dead_body() {
        $b = new Model_Items_Body('Verstörter Patient', 'Der Patient trägt ein Identifikationsarmband, auf dem sich ein Barcode sowie ein Name befindet. Du wirst wohl nie erfahren, wer das war oder was mit ihm in der Irrenanstalt geschehen ist. Wobei... vermutlich willst du das auch lieber gar nicht wissen.');
        $b->give_name(Model_User::random_names(1)[0]);
        return $b;
    }

    public function kill() {
        if ($this->location() && !$this->is_aggresive) $this->location()->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, $this->id(), true));
        else parent::kill();
    }

    protected function handle_death() {
        return !$this->is_passified;
    }

    public function hid() {
        return parent::hid()
            ->add_action('Bestehlen', Model_Action::factory()
                ->show_as(Model_Effect::factory()
                    ->ambiguous_effect()
                )
                ->effect(Model_Effect::factory()
                    ->custom(function($p) {
                        /** @var Model_Player $p */
                        $p->log()->add('Als du versuchst nach ihm zu greifen, beginnt der Patient markerschütternd zu schreien und greift an!');
                        $this->is_aggresive = true;

                        $cpl = [];
                        foreach (Tool_Scripts::at_location($p->location_class()) as $npl)
                            if ($npl->id() != $this->id())
                                $cpl[] = $npl;

                        Tool_Scripts::combat([$cpl,[$this]],false,2,$p->location(),'Der verstörte Patient greift an!');
                    })
                )
            )
            ->add_action('Teddy geben', Model_Action::factory()
                ->requirement("Model_Items_Generic_Teddy", 1)
                ->effect(Model_Effect::factory()
                    ->custom(function($p) {
                        /** @var Model_Player $p */
                        $p->log()->add('Du hälst ihm deinen Teddy hin. Er sieht ihn mit glasigen Augen an, und greift nach ein paar Sekunden zu. Irgendetwas scheint ihn enttäuscht zu haben, denn er schleicht mit hängenden Schultern davon.');

                        $items = [];
                        foreach ($this->inventory()->get() as $item) {
                            $this->inventory()->remove($item->uin());
                            $items[] = $item;
                        }

                        Tool_Scripts::place_new_item($items, 'Der Patient hat seine Gegenstände fallen gelassen, als du ihm den Teddy gegeben hast.');

                        $this->is_passified = true;
                        $this->kill();
                    })
                )
            )
            ->add_action('Anderen Teddy geben', Model_Action::factory()
                ->requirement("Model_Items_Generic_Cursed", 1)
                ->effect(Model_Effect::factory()
                    ->custom(function($p) {
                        /** @global Model_Game $game */
                        global $game;

                        /** @var Model_Player $p */
                        $p->log()->add('Du hälst ihm deinen Teddy hin. Eine Träne läuft ihm aus dem Auge, dann greift er zu und drückt den Teddy fest an sich. Eine Weile verharrt er regungslos, dann zeigt er mit dem Finger auf einen dunklen Gang, der dir bisher verborgen geblieben ist. Als du dich wieder zu ihm umdrehst, ist er verschwunden...');

                        $items = [];
                        foreach ($this->inventory()->get() as $item) {
                            $this->inventory()->remove($item->uin());
                            $items[] = $item;
                        }

                        Tool_Scripts::place_new_item($items, 'Der Patient hat seine Gegenstände fallen gelassen, als du ihm den Teddy gegeben hast.');

                        $mid = "submap_ashide_{$this->location()->uin()}";
                        $slid = $game->register_map($mid, 'ashide');
                        if ($slid) {
                            $this->location()->register_doorway($slid);
                            $game->location($slid)->register_doorway($this->location()->uin());
                        }

                        $ev = $game->get_initialized_event(Model_Events_Halloween::get_key());
                        /** @var $ev Model_Events_Halloween */
                        if ($ev) $ev->register_event_map($mid);

                        $this->is_passified = true;
                        $this->kill();
                    })
                )
            )
            ;
    }
}