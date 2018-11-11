<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Plant extends Model_Items_Abstract_Virtual {

    protected static $graceful_fail = true;
    protected $remaining = array(
        'vent_open' => 1
    );

    protected function hid() {
        return parent::hid()->add_action('Ventile des Kühlkreislaufes öffnen', Model_Action::factory()
            ->buttonskin('location')
            ->requirement(Model_Status::MS_STAT_ENERGY, 30)
            ->show_as(Model_Effect::factory()
                ->ambiguous_effect()
            )
            ->decider(function() {
                $res =  Array(
                    Array('chance' => 1, 'value' => 'd1'),
                    Array('chance' => 1,  'value' => 'd2'),
                );

                return Tool_Gambling::roulette($res);
            })
            ->effect(Model_Effect::factory()
                ->message('Du stemmst dich mit aller Kraft gegen das Ventil. Mit einem Schlag öffnet es sich, und ein Schwall Kühlwasser ergießt sich über dich. Das lindert zwar sofort deinen Durst, leider bist du jetzt auch gewaltig verstrahlt worden...')
                ->custom(function($p) {
                        /** @var Model_Player $p */
                        $r_drinks = random_int(2, 6);
                        for ($i = 0; $i < $r_drinks; $i++) $p->location()->inventory()->add(new Model_Items_Generic_Water2());
                        $p->get_status()->modify(Model_Status::MS_STAT_RADIATION, random_int(20,50), Model_Status::MS_STAT_THIRST, 100);
                })
            , 'd1')
            ->effect(Model_Effect::factory()
                ->message('Du stemmst dich mit aller Kraft gegen das Ventil. Als du es endlich geöffnet hast, stellst du fest dass das ganze Kühlwassersystem schon fast leergelaufen war. Naja, wenigstens ein paar Rationen konntest du dir noch sichern...')
                ->custom(function($p) {
                        /** @var Model_Player $p */
                        $r_drinks = random_int(1, 3);
                        for ($i = 0; $i < $r_drinks; $i++) $p->location()->inventory()->add(new Model_Items_Generic_Water1());
                })
            , 'd2')
        , 'vent_open');
    }
}	