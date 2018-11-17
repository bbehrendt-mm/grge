<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Pillboxes extends Model_Items_Abstract_Virtual {

    private $spawn_twinoid = false;

    protected static $graceful_fail = true;
    protected static $default_action_uses = array(
        'find_pills' => 1
    );

    public function __construct($twinoid = false) {
        parent::__construct();
        $this->spawn_twinoid = $twinoid;
    }

    protected function hid(): Model_Hid {
        return parent::hid()->add_action('Pillenschachteln durchwühlen', Model_Action::factory()
            ->buttonskin('location')
            ->description('Überall auf dem Boden liegen geöffnete Pillenschachteln. Vermutlich könntest du hier noch einige einzelne Pillen finden, wenn du dich anstrengst.')
            ->show_as(Model_Effect::factory()
                ->ambiguous_effect()
            )
            ->requirement(Model_Status::MS_STAT_ENERGY, 10)
            ->effect(Model_Effect::factory()
                ->custom(function($p) {
                    /** @var Model_Player $p */
                    $items = Array();
                    $r_pills = random_int(5, 20);
                    $r_para = random_int(0, 6);
                    for ($i = 0; $i < $r_pills; $i++) $items[] = new Model_Items_Pill();
                    for ($i = 0; $i < $r_para; $i++) {
                        $s = Tool_Gambling::roulette(Array(
                            Array('chance' => $this->spawn_twinoid ? 2 : 4, 'value' => Model_Items_Paracetoid::cls()),
                            Array('chance' => $this->spawn_twinoid ? 1 : 3, 'value' => Model_Items_Paracetin::cls()),
                            Array('chance' => $this->spawn_twinoid ? 1 : 2, 'value' => Model_Items_Foodsupplement::cls()),
                            Array('chance' => $this->spawn_twinoid ? 4 : 0, 'value' => Model_Items_Twinoid::cls()),
                        ));
                        $items[] = new $s;
                    }
                    Tool_Scripts::place_new_item($items);
                    $p->log()->add(new Model_Log_Types_String( null, 'Es war eine langwierige Fummelarbeit, aber am Schluss hat es sich gelohnt. Du hast einen ganzen Haufen Pillen zusammentragen können. Jetzt gilt es nur hoch herauszufinden, wofür diese Pillen gut sind ...'));
                })
            )
        , 'find_pills');
    }
}	