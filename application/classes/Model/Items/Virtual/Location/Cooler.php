<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Cooler extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'cooler_open' => 1
    );

    protected function hid() {
        $phpbb53 = $this;
        return parent::hid()->add_action('Kühlkammer aufbrechen', Model_Action::factory()
                ->description('Der Kühlraum ist fest verschlossen. Es sieht nicht so aus, als wäre er nach der Apokalypse noch einmal geöffnet worden... vielleicht findest du etwas nützliches darin?')
                ->requirement(Model_Player::MP_STAT_ENERGY, 25)
                ->show_as(Model_Effect::factory()
                    ->ambiguous_effect()
                )
                ->decider(function() {
                    $res =  Array(
                        Array('chance' => 25, 'value' => 'stuff'),
                        Array('chance' => 15,  'value' => 'matress'),
                        Array('chance' => 5,  'value' => 'zombie')
                    );

                    return Tool_Gambling::roulette($res);
                })
                ->effect(Model_Effect::factory()
                    ->message('Die schwere Metalltür, die die Küche vom Kühlraum trennt, ist inzwischen startk verrostet. Nach einiger Anstrengung gelingt es dir aber doch, sie einen Spalt zu öffnen. Du willst gerade hineingehen, als du plötzlich von einem Zombie angefallen wirst! Wie zum Teufel ist der denn da rein gekommen? Zu allem Überfluss ist der Kühlraum (bis auf den Zombie) völlig leer...')
                    ->custom(function() {
                        Tool_Scripts::simple_battle(1, 0, "Ein angriffslustiger Zombie springt aus dem Kühlraum und greift an!", true, false);
                    })
                , 'zombie')
                ->effect(Model_Effect::factory()
                    ->message('Die schwere Metalltür, die die Küche vom Kühlraum trennt, ist inzwischen startk verrostet. Nach einiger Anstrengung gelingt es dir aber doch, sie einen Spalt zu öffnen. Als du hineinschaust erlebst du jedoch eine böse überraschung: Die Arbeiter in diesem Restaurant scheinen sich beim Ausbruch der Epidemie im Kühlraum versteckt zu haben! Alle Vorräte sind aufgebraucht, nur ein paar magere Leichen und eine alte Matratze liegen noch herum!')
                    ->custom(function() {
                            Tool_Scripts::place_new_item(new Model_Items_Generic_Bed());
                    })
                , 'matress')
                ->effect(Model_Effect::factory()
                        ->message('Die schwere Metalltür, die die Küche vom Kühlraum trennt, ist inzwischen startk verrostet. Nach einiger Anstrengung gelingt es dir aber doch, sie einen Spalt zu öffnen. Für diese Leistung kannst du dich nun selbst mit einem Fastfood-Festmahl beglücken!')
                        ->custom(function() {
                            $items = array();
                            $r_food = mt_rand(2, 10);
                            $r_drinks = mt_rand(3, 8);
                            for ($i = 0; $i < $r_food; $i++) $items[] = new Model_Items_Fastfood();
                            for ($i = 0; $i < $r_drinks; $i++) $items[] = new Model_Items_Softdrink();
                            Tool_Scripts::place_new_item($items);
                        })
                    , 'stuff')
            , 'cooler_open');
    }
}	