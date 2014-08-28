<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Muscle extends Model_Items_Abstract_Virtual {

    public function __construct($level = 1) {
        $this->remaining = array(
            'hero_job_0' => 1,
            'hero_job_1' => $level >= 6 ? 1 : 0,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid() {
        global $player;
        return parent::hid()
            ->add_action('Workout', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Bekämpft Müdigkeit und regeneriert Energie. Der Effekt ist abhängig von Hunger, Durst und Gewicht des Rucksacks - je voller der Rucksack, desto besser.')
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Player::MP_STAT_ENERGY, $player->inventory()->weight() * ($player->stats_get(Model_Player::MP_STAT_HUNGER)/100) * ($player->stats_get(Model_Player::MP_STAT_THIRST)/100))
                        ->effect(Model_Player::MP_STAT_SLEEPY, $player->inventory()->weight() * ($player->stats_get(Model_Player::MP_STAT_HUNGER)/100) * ($player->stats_get(Model_Player::MP_STAT_THIRST)/100))
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