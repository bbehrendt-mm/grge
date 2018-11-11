<?php

class Model_NPC_Event_Conductor extends Model_NPC_Humanoid
{
    protected static $buffs_heartbeat_name = 'Model_Buffs_Event_Fakeheart';
    protected static $buffs_metabolism_name = 'Model_Buffs_Event_Fakemetabolism';
    protected static $buffs_place_various = false;

    protected static $abillities = [];

    protected static $handle_death = false;

    public function __construct($name = null) {
        parent::__construct('Der Schaffner');
    }

    public function entity_action() {
        return 'Wartet auf Passagiere';
    }

    public function entity_description() {
        return 'Der Schaffner scheint darauf zu warten, dass du ihm etwas gibst... wie wäre es zum Beispiel mit einem Goldenen Ticket?';
    }

    public function is_fighter() {
        return false;
    }

    public function kill() {
        if ($this->location()) $this->location()->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, $this->id(), true));
        parent::kill();
    }

    public function hid() {
        return parent::hid()
            ->add_action('Ticket übergeben', Model_Action::factory()

                ->requirement(Model_Items_Generic_Ticket::cls(), 1)
                ->effect(Model_Effect::factory()
                    ->message('Du schließt für einen Moment deine Augen... als du sie wieder öffnest, stehst du plötzlich auf einem verlassenen Weihnachtsmarkt! In der Mitte des Markts steht eine leere Weihnachtsbaum-Halterung. Wie traurig... du solltest dich vom Geist der Weihnacht erfüllen lassen und dort einen wunderschön geschmückten Weihnachtsbaum aufstellen! Sicherlich wirst du dafür genug Materialien hier finden...')
                    ->custom(function($p)  {
                        /** @var Model_Player $p */

                        $ev = Globals::CurrentGameF()->get_initialized_event(Model_Events_Xmas::get_key());
                        /** @var $ev Model_Events_Xmas */
                        if (!$ev) return;

                        $tid = time() . '_' . mt_rand();
                        $mapid = "xmasmap_{$tid}";
                        $xmas_id = Globals::CurrentGameF()->register_map($mapid, 'xmas', 'xmas');
                        $xmasfair = Globals::CurrentGameF()->location($xmas_id);
                        if ($xmasfair === null) return;

                        $xmasfair->register_doorway($p->location_class());

                        $p->location()->leave_map($p->id());
                        $p->location_class($xmas_id);
                        $xmasfair->enter_map($p->id());

                        Globals::CurrentGameF()->mapF($xmas_id)->movement_modifier(0.1);
                        $ev->register_event_map($mapid);

                        if (!$p->get_status()->retrieve('freeze'))
                            new Model_Buffs_Freeze($p->id());
                    })
                )
            );
    }
}