<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Eclair extends Model_Items_Abstract_Virtual {

    public function __construct($level = 1) {
        parent::__construct();
        $this->remaining = array(
            'hero_job_0' => $level * 2,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid() {
        return parent::hid()
            ->add_action('Taktischer Rückzug', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Ermöglicht dir die Flucht aus einer von Zombies belagerten Zone, wirkt sich jedoch nicht auf andere Spieler auf deiner Zone aus!')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $p->enable_escape();
                        })
                        ->effect(Model_Status::MS_STAT_THIRST, PHP_INT_MAX)
                )
            , 'hero_job_0');
    }
}	