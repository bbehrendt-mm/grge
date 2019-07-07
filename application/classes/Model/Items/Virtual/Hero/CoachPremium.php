<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_CoachPremium extends Model_Items_Abstract_Virtual {

    protected static $graceful_fail = true;

    public function __construct($level = 2) {
        parent::__construct();
        $this->remaining = array(
            'hero_job_premium_0' => $level,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Demenz-Anfall', Model_Action::factory()
                    ->buttonskin('hero hja')
                    ->description('Dank deiner über viele Jahre gesammelten Gehirnverletzungen kannst du bei Bedarf einfach vergessen, dass du Hunger oder Durst hast oder verletzt, müde oder drogenabhängig bist.')
                    ->effect(
                        Model_Effect::factory()
                            ->effect( Model_Status::MS_STAT_HUNGER, 100)
                            ->effect( Model_Status::MS_STAT_THIRST, 100)
                            ->effect( Model_Status::MS_STAT_HEALTH, 100)
                            ->effect( Model_Status::MS_STAT_SLEEPY, 100)
                            ->effect( Model_Status::MS_STAT_RADIATION, -100)
                            ->effect( Model_Status::MS_STAT_DRUNK, -100)
                            ->buff(Model_Buffs_Drug1::cls(), true)
                            ->buff(Model_Buffs_Drug2::cls(), true)
                            ->buff(Model_Buffs_Drug3::cls(), true)
                            ->buff(Model_Buffs_Blood::cls(), true)
                            ->buff(Model_Buffs_Bite::cls(), true)
                            ->buff(Model_Buffs_Hallucinations::cls(), true)
                    )
                , 'hero_job_premium_0');
    }
}	