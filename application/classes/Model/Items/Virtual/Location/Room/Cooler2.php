<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Room_Cooler2 extends Model_Items_Abstract_Virtual {

    protected function self_upgrade(): void {
        $this->roomF()->clear();
    }

    protected function hid(): Model_Hid {
        return parent::hid()->add_action('Kühlsystem abbauen', Model_Action::factory()
            ->buttonskin('location')
            ->description('Der Kühlraum verfügt über ein äußerst effizientes Kühlsystem. Du könntest es auseinander nehmen und etwas neues aus den Einzelteilen bauen.')
            ->requirement(Model_Status::MS_STAT_ENERGY, 70)
            ->effect(Model_Effect::factory()
                ->message('Mit aller Kraft konntest du sämtliche Komponenten des Kühlraums aus der Wand reißen. ')
                ->consume($this)
                ->spawn(Model_Items_Generic_Cooler::cls(), 2)
                ->spawn(Model_Items_Generic_Electro::cls(), 2)
                ->spawn(Model_Items_Generic_Tube::cls(), 3)
                ->spawn(Model_Items_Generic_Sum::cls(), 5)
        ));
    }
}	