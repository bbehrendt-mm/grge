<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Market extends Model_Items_Abstract_Virtual {

    protected static $graceful_fail = true;
    protected $remaining = array(
        'wagon_open' => 1
    );

    protected function hid() {
        $phpbb53 = $this;
        return parent::hid()->add_action('Marktwagen freilegen', Model_Action::factory()
            ->buttonskin('location')
            ->requirement(Model_Status::MS_STAT_ENERGY, 10)
            ->show_as(Model_Effect::factory()
                ->ambiguous_effect()
            )
            ->decider(function() {
                $res =  Array(
                    Array('chance' => 5, 'value' => 'd0'),
                    Array('chance' => 5,  'value' => 'd1'),
                    Array('chance' => 1,  'value' => 'd2')
                );

                return Tool_Gambling::roulette($res);
            })
            ->effect(Model_Effect::factory()
                ->message('Du schiebst ein paar Trümmer sowie eine zerissene Plane beiseite und siehst, dass der Wagen mit Lebensmitteln beladen war! Welch ein Festmahl!')
                ->custom(function() {
                        $items = array();
                        $r_food = mt_rand(2, 20);
                        for ($i = 0; $i < $r_food; $i++) $items[] = new Model_Items_Basefood();
                        Tool_Scripts::place_new_item($items);
                })
            , 'd0')
            ->effect(Model_Effect::factory()
                ->message('Du schiebst ein paar Trümmer sowie eine zerissene Plane beiseite und siehst, dass der Wagen mit Baumaterialien beladen war! Welch ein Glück!')
                    ->custom(function() {
                        $items = array();
                        $r_wood = mt_rand(0, 3);
                        $r_metal = mt_rand(0, 3);
                        $r_sum = mt_rand(0, 3);
                        $r_duct = mt_rand(0, 3);
                        $r_electro = mt_rand(0, 3);
                        $r_tube = mt_rand(0, 3);
                        $r_cloth = mt_rand(0, 3);
                        for ($i = 0; $i < $r_wood; $i++) $items[] = new Model_Items_Generic_Wood();
                        for ($i = 0; $i < $r_metal; $i++) $items[] = new Model_Items_Generic_Metal();
                        for ($i = 0; $i < $r_sum; $i++) $items[] = new Model_Items_Generic_Sum();
                        for ($i = 0; $i < $r_duct; $i++) $items[] = new Model_Items_Generic_Ducttape();
                        for ($i = 0; $i < $r_electro; $i++) $items[] = new Model_Items_Generic_Electro();
                        for ($i = 0; $i < $r_tube; $i++) $items[] = new Model_Items_Generic_Tube();
                        for ($i = 0; $i < $r_cloth; $i++) $items[] = new Model_Items_Generic_Cloth();
                        Tool_Scripts::place_new_item($items);
                })
            , 'd1')
            ->effect(Model_Effect::factory()
                    ->message('Du schiebst ein paar Trümmer sowie eine zerissene Plane beiseite und siehst, dass der Wagen leer ist. Wobei, leer trifft es nicht ganz... ')
                    ->custom(function() {
                        $items = array();
                        $items[] = new Model_Items_Body('Vera Loewenhaupts Sohn', 'Dies scheint einer der Söhne von Vera Loewenhaupt zu sein...');
                        $items[] = new Model_Items_Body('Vera Loewenhaupts anderer Sohn', 'Dies scheint einer der Söhne von Vera Loewenhaupt zu sein...');
                        Tool_Scripts::place_new_item($items);
                    })
                , 'd2')
        , 'wagon_open');
    }
}	