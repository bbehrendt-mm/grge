<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Soldier extends Model_Items_Abstract_Virtual {

    public function __construct($level = 1) {
        parent::__construct();
        $actions = $level >= 10 ? 1 : 5;
        $this->remaining = array(
            'hero_job_0' => $actions,
            'hero_job_1' => $level >= 6 ? $actions : 0,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Taktik', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Die nächsten 20 Minuten lang wird dein Kampfschaden erhöht.')
                ->effect(
                    Model_Effect::factory()
                        ->buff('Model_Buffs_Tactics', false, 4)
                        ->message('Ein guter Soldat kennt seine Umgebung - und nutzt sie zu seinem Vorteil. Wenn du jetzt gegen Zombies kämpfst, werden die ihr blaues Wunder erleben!')
                )
            , 'hero_job_0')
            ->add_action('Vietnam Flashback', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Tötet sämtliche Zombies, die deinen Aufenthaltsort belagern.')
                ->effect(
                    Model_Effect::factory()
                        ->achieve(Model_Achievement::MA_KILLED_ZOMBIES, Globals::CurrentPlayerF()->location()->zombie_pop())
                        ->custom(function($p) {
                            /** @var $p Model_Player */
                            $p->location()->zombie_pop(true);
                        }, Model_Effect::CFUNC_PROCESS_POST)
                        ->message('Die Welt um dich herum verschwimmt... du hörst Kanoneneinschläge, Hubschrauber, Maschinengewehrfeuer... als du wieder zu dir kommst, stehst du inmitten eines Haufens aus Leichen. Puh, wenigstens ist dir das diesmal nicht wieder während der Schulaufführung deines Sohnes passiert.')
                )
            , 'hero_job_1');
    }
}	