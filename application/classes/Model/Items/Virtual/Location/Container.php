<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Container extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'container_open' => 3
    );

    protected function hid() {
        $phpbb53 = $this;
        return parent::hid()->add_action('Einen Container öffnen', Model_Action::factory()
                ->description('Hier stehen einige Container herum. Da du nicht hereinschauen kannst, musst du sie wohl aufmachen, um herauszufinden, was drin ist.')
                ->show_as(Model_Effect::factory()
                    ->ambiguous_effect()
                )
                ->effect(Model_Effect::factory()
                    ->custom(function($p) {
                            /** @var Model_Player $p */

                            Tool_Scripts::simple_battle(mt_rand(2, 10),0, "Eine Gruppe Zombies stürmt aus dem Container und greift an!", false, false);

                            if ($p->alive())
                            {
                                $r_lbs = mt_rand(1, 5);
                                $r_chairs = mt_rand(0, 2);
                                $r_band = mt_rand(0, 1);
                                $items = Array();
                                for ($i = 0; $i < $r_lbs; $i++) $items[] = new Model_Items_Lunchbag();
                                for ($i = 0; $i < $r_chairs; $i++) if (mt_rand(0, 2) == 2) $items[] = new Model_Items_Gardenchair2(); else $items[] = new Model_Items_Gardenchair();
                                for ($i = 0; $i < $r_band; $i++) $items[] = new Model_Items_Bandage();
                                if (mt_rand(1, 10) > 9) $items[] = new Model_Items_Ammo();

                                Tool_Scripts::place_new_item($items);
                            }
                    })
                )
            , 'container_open');
    }
}	