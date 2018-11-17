<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Hunter extends Model_Items_Abstract_Virtual {

    public function __construct($level = 1) {
        parent::__construct();
        $this->remaining = array(
            'hero_job_0' => $level * 2,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Selbstversorgung', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Regeneriert Hunger und Durst.')
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, PHP_INT_MAX)
                        ->effect(Model_Status::MS_STAT_THIRST, PHP_INT_MAX)
                )
            , 'hero_job_0');
    }
}	