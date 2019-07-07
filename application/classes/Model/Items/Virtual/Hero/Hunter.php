<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Hunter extends Model_Items_Abstract_Virtual {

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
            ->add_action('Selbstversorgung', Model_Action::factory()
                ->buttonskin($str)
                ->description('Regeneriert Hunger und Durst.')
                ->decider(function(Model_Player $p) {
                    /** @var Model_Player $p */
                    return (!$this->wunderkind || !Tool_Gambling::tumble($p)) ? 0 : 1;
                })
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, PHP_INT_MAX)
                        ->effect(Model_Status::MS_STAT_THIRST, PHP_INT_MAX)
                )
                ->effect(
                    Model_Effect::factory()
                        ->buff(Model_Buffs_Hallucinations::cls(), false, 48)
                        ->effect(Model_Status::MS_STAT_HUNGER, PHP_INT_MAX)
                        ->effect(Model_Status::MS_STAT_THIRST, PHP_INT_MAX)
                        ->effect(Model_Status::MS_STAT_THIRST, PHP_INT_MAX)
                        ->message('Doppelt sehen ist wenig hilfreich bei der genauen Identifikation von gesammelten Pilzen...')
                )
            , 'hero_job_0');
    }
}	