<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Room_Container extends Model_Items_Abstract_Virtual {

    protected function hid(): Model_Hid {
        return parent::hid()->add_action('Container öffnen', Model_Action::factory()
            ->buttonskin('location')
            ->description('Hier steht ein verschlossener Baucontainer. Da du nicht hereinschauen kannst, musst du ihn wohl aufmachen, um herauszufinden, was drin ist.')
            ->show_as(Model_Effect::factory()
                ->ambiguous_effect()
            )
            ->effect(Model_Effect::factory()
                ->consume($this)
                ->custom(function($p) {
                    /** @var Model_Player $p */

                    Tool_Scripts::simple_battle(random_int(2, 10),0,
                        'Eine Gruppe Zombies stürmt aus dem Container und greift an!', false, false);

                    if ($p->get_status()->alive())
                    {
                        $r_lbs = random_int(1, 5);
                        $r_chairs = random_int(0, 2);
                        $r_band = random_int(0, 1);
                        $items = Array();
                        for ($i = 0; $i < $r_lbs; $i++) $items[] = new Model_Items_Lunchbag();
                        for ($i = 0; $i < $r_chairs; $i++) if (random_int(0, 2) === 2) $items[] = new Model_Items_Gardenchair2(); else $items[] = new Model_Items_Gardenchair();
                        for ($i = 0; $i < $r_band; $i++) $items[] = new Model_Items_Bandage();
                        if (random_int(1, 10) > 9) $items[] = new Model_Items_Ammo();

                        Tool_Scripts::place_new_item($items);
                    }

                    Model_Blueprints::fast_apply($this->location(),'rooms','free', $this->room());
                })
            )
        );
    }
}	