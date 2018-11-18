<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Pathfinder extends Model_Items_Abstract_Virtual {

    public function __construct($level = 1) {
        parent::__construct();
        $this->remaining = array(
            'hero_job_0' => 1,
            'hero_job_1' => $level >= 6 ? 1 : 0,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Wegschleichen', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Ermöglicht dir eine sichere Flucht vor blockierenden Zombies.')
                ->condition(function($p) {
                        /** @var $p Model_Player */
                        return ($p->location()->zombie_pop() > 0);
                    })
                ->fail_message('Super, du bist geflohen... leider vor NIEMANDEM, den hier sind gar keine Zombies.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var $p Model_Player */
                            $p->enable_escape();
                        })
                        ->message('Als Pfadfinder kannst du natürlich problemlos einen Pfad finden, der an der Zombieblockade vorbei führt. Nun aber schell weg hier!')
                )
            , 'hero_job_0')
            ->add_action('Marsch', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Für die nächste Reise musst du keine Energie aufbringen.')
                ->effect(
                    Model_Effect::factory()
                        ->buff('Model_Buffs_Move')
                        ->message('Einmal tief durchatmen, dann kanns losgehen. Die nächste Reise wird ein Klacks für dich!')
                )
            , 'hero_job_1');
    }
}	