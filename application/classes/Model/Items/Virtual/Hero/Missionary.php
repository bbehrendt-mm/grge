<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Missionary extends Model_Items_Abstract_Virtual {

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

    protected function hid() {
        return parent::hid()
            ->add_action('Kasteien', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Verbraucht 50% deiner Gesundheit und wandelt die Hälfte davon in Energie um.')
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, -floor(Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_STAT_HEALTH)/2))
                        ->effect(Model_Status::MS_STAT_ENERGY, floor(Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_STAT_HEALTH)/4))
                        ->message('Statt darauf zu warten dass Gott dich für deine vielen Sünden bestrafst, kannst du das auch einfach selbst tun. Dieses hochspirituelle Erlebnis verschafft dir neue Energie und verstörender Weise auch eine Menge Befriedigung...')
                )
            , 'hero_job_0')
            ->add_action('Wundersame Heilung', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Heilt sämtliche Wunden und stellt deine Gesundheit wieder her.')
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, PHP_INT_MAX)
                        ->effect(Model_Status::MS_STAT_DRUNK, -PHP_INT_MAX)
                        ->effect(Model_Status::MS_STAT_RADIATION, -PHP_INT_MAX)
                        ->effect(Model_Status::MS_STAT_ZOMBIFY, -PHP_INT_MAX)
                        ->buff('Model_Buffs_Blood', true)
                        ->buff('Model_Buffs_Bite', true)
                        ->buff('Model_Buffs_Drug1', true)
                        ->buff('Model_Buffs_Drug2', true)
                        ->buff('Model_Buffs_Drug3', true)
                        ->message('Ein Wunder ist geschehen! All deine Gebrechen wurden geheilt!')
                )
            , 'hero_job_1');
    }
}	