<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Roadblock extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'barricade_open' => 7
    );
    protected static $graceful_fail = true;

    private static $elist = array(7 => 5, 6 => 10, 5 => 15, 4 => 30, 3 => 45, 2 => 70, 1 => 95, 0 => 101);

    protected function hid() {
        $phpbb53 = $this;
        return parent::hid()->add_action('Barrikade abbauen', Model_Action::factory()
            ->buttonskin('location')
            ->description('Diese Barrikade sieht ziemlich stabil aus, aber wenn du dich etwas ins Zeug legst kannst du hier bestimmt das eine oder andere nützliche Item ausbauen.')
            ->requirement(Model_Player::MP_STAT_ENERGY, static::$elist[$this->remaining['barricade_open']])
            ->show_as(Model_Effect::factory()
                ->ambiguous_effect()
            )
            ->effect(Model_Effect::factory()
                ->custom(function($p) {
                        /** @var Model_Player $p */

                        $items = array('Model_Items_Generic_Table','Model_Items_Generic_Tube','Model_Items_Generic_Metal','Model_Items_Generic_Bed','Model_Items_Generic_Cloth','Model_Items_Generic_Oven','Model_Items_Generic_Motor','Model_Items_Generic_Wood', 'Model_Items_Generic_Pumpkin');

                        if (mt_rand(0,10) < 2)
                            Tool_Scripts::simple_battle(1,0, "Verflucht! Nachdem du etwas Schutt aus dem Weg geräumt hast, springt dich ein Zombie an! Wie zur Hölle ist der da nur rein gekommen??", true, false);

                        else {
                            $p->log()->add('Es hat dich etwas Arbeit gekostet, aber du konntest etwas nützliches aus dieser Barrikade herauszerren.');
                            $item = $items[mt_rand(0,count($items)-1)];
                            $count = (mt_rand(0,10) > 8) ? 2 : 1;
                            for ($i = 0; $i < $count; $i++) $tmp[] = new $item;
                            Tool_Scripts::place_new_item($tmp);
                        }
                })
            )
        , 'barricade_open');
    }
}	