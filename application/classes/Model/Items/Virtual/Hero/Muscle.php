<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Muscle extends Model_Items_Abstract_Virtual {

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
            ->add_action('Workout', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Bekämpft Müdigkeit und regeneriert Energie. Der Effekt ist abhängig von Hunger, Durst und Gewicht des Rucksacks - je voller der Rucksack, desto besser.')
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_ENERGY, Globals::CurrentPlayerF()->inventory()->weight() * (Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_STAT_HUNGER)/100) * (Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_STAT_THIRST)/100))
                        ->effect(Model_Status::MS_STAT_SLEEPY, Globals::CurrentPlayerF()->inventory()->weight() * (Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_STAT_HUNGER)/100) * (Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_STAT_THIRST)/100))
                        ->message('So ein Workout wirkt Wunder! Nach ein paar Liegestützen und Kniebeugen bist du wieder Fit für den Kampf um Leben und Tod.')
                )
            , 'hero_job_0')
            ->add_action('Hulkout', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Erhöht Gewichtslimit um 70 für 15 Minuten.')
                ->effect(
                    Model_Effect::factory()
                        ->buff('Model_Buffs_Hulk', false, 3)
                        ->message('Das soll ich nicht heben können? VON WEGEN! ')
                )
            , 'hero_job_1');
    }
}	