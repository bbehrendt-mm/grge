<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Eclair extends Model_Items_Abstract_Virtual {

    protected $wunderkind;

    public function __construct($level = 1, bool $as_wunderkind = false) {
        parent::__construct();
        $this->wunderkind = $as_wunderkind;
        $this->remaining = array(
            'hero_job_0' => $level * 2,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid(): Model_Hid {
        $str = $this->wunderkind && Globals::CurrentPlayerActual()->get_status()->get(Model_Status::MS_STAT_DRUNK) >= 15 ? 'hero action-drunk hja' : 'hero hja';

        return parent::hid()
            ->add_action('Taktischer Rückzug', Model_Action::factory()
                ->buttonskin($str)
                ->condition(function(Model_Player $p) {
                    return $p->location()->zombie_pop() > 0;
                })
                ->fail_message("Hier gibt es nichts, wovor du flüchten könntest.")
                ->description('Ermöglicht dir die Flucht aus einer von Zombies belagerten Zone, wirkt sich jedoch nicht auf andere Spieler auf deiner Zone aus!')
                ->decider(function(Model_Player $p) {
                    /** @var Model_Player $p */
                    return !$this->wunderkind || !Tool_Gambling::tumble($p) ? 0 : 1;
                })
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $p->enable_escape();
                        })
                )
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $p->log()->add('Wenn du noch in der Lage wärst, geradeaus zu laufen, hätte das vielleicht geklappt.');
                            $p->location()->break_out(true);
                        })
                )
            , 'hero_job_0');
    }
}	