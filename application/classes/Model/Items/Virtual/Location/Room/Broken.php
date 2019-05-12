<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Room_Broken extends Model_Items_Abstract_Virtual {

    protected $stage = 0;

    protected function hid(): Model_Hid {
        return $this->stage == 0 ? parent::hid()->add_action('Freiräumen', Model_Action::factory()
            ->buttonskin('location')
            ->description('Dieser Raum ist voller Schutt. Bevor die ihn nutzen kannst, musst du erst einmal ein wenig aufräumen.')
            ->requirement(Model_Status::MS_STAT_ENERGY, 95)
            ->show_as(Model_Effect::factory()
                ->ambiguous_effect()
            )
            ->effect(Model_Effect::factory()
                 ->message( "Nachdem du den Schutt beseitigt hast stellst du fest, dass die Wände und Decke schwer beschädigt sind." )
                 ->custom(function($p) {
                    $this->stage++;
                })
            )
        ) : parent::hid()->add_action('Reparieren', Model_Action::factory()
            ->buttonskin('location')
            ->description('Dieser Raum ist eine Todesfalle! Bevor du die Wände und Decke nicht abgestützt hast, kannst du ihn nicht nutzen.')
            ->requirement(Model_Status::MS_STAT_ENERGY, 25)
            ->requirement(Model_Items_Generic_Wood::cls(), 15)
            ->requirement(Model_Items_Generic_Metal::cls(), 10)
            ->requirement(Model_Items_Generic_Sum::cls(), 2)
            ->show_as(Model_Effect::factory()
                  ->ambiguous_effect()
            )
            ->effect(Model_Effect::factory()
                 ->consume($this)
                 ->message( "Na bitte - mit nur ein wenig Arbeit und unter minimalem Ressourceneinsatz hast du diesen Raum nutzbar gemacht!" )
                 ->custom(function($p) {
                     Model_Blueprints::fast_apply($this->location(),'rooms','free', $this->room());
                     $this->room()->set_default_state();
                 })
            )
        );
    }
}	