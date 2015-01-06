<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Home extends Model_Places_Abstract_Hideout {

    protected static $name = 'Versteck';
    protected static $description = 'In deinem Versteck bist du vor Zombieangriffen geschützt und kannst dich von deinen Aktionen in der Aussenwelt erholen - zumindest, wenn du dich gut verbarrikadiert hast! Unglücklicherweise kannst du nicht für immer hier sitzen bleiben - das wirst du spätestens dann merken, wenn deine gesammelten Vorräte aufgebraucht sind ...';
    protected static $icon = 'home';

    //Base defense
    protected $defense = 5;

    //Base: 15% per day
    protected static $decay_rate = 0.15;

    //Exp: 8% per day
    protected static $decay_exp = 0.08;

    private  $map_points = 0;

    public function get_map_points() {
        return $this->map_points;
    }

    public function set_map_points($new) {
        $this->map_points = $new;
    }


    public function uin($new = null) {
        global $game;
        if ($new !== null) {
            $this->add_upgrades('hideout');
            $this->set_decay(0, true);

            if ($game->config('modules.mapping'))
                $this->inventory->add(new Model_Items_Virtual_Location_Mapmode());
        }
        return parent::uin($new);
    }

    protected function create_npcs() {
        global $game;
        $ret = parent::create_npcs();

        $item_gen = function($p)  {
            /** @var $p Model_Player */
            global $game;

            $locations = $game->map()->build_route_array($this->uin());
            foreach ($locations as $key => $data) {
                if (Tool_Scripts::location_type($key) !== 0)
                    unset($locations[$key]);
                elseif (count($game->location($key)->inventory()->get('Model_Items_Abstract_Easteregg')) > 0)
                    unset($locations[$key]);
            }
            $locations = array_keys($locations);

            if (count($locations) < 2) {
                $p->log()->add(new Model_Log_Types_Text(null,null,'KRAAHAHAHA! So ein Pech! Du kennst nicht genügend Orte! KRARAAAAH!'));
                return;
            } else {
                //Place egg1
                shuffle($locations);
                $left = $num = mt_rand(count($locations), 3*count($locations));
                foreach ($locations as $key) {
                    $put = mt_rand(min($left, ceil(count($locations)/5)), min($left, ceil(count($locations)/2)));
                    $left -= $put;
                    for ($i = 0; $i < $put; $i++)
                        $game->location($key)->inventory()->add(new Model_Items_Generic_Egg1(1,true));
                }
                $placed1 = $num - $left;

                //Place egg2
                shuffle($locations);
                $left = $num = mt_rand(0, floor(count($locations)/4));
                foreach ($locations as $key) if ($left > 0) {
                    $left -= 1;
                    $game->location($key)->inventory()->add(new Model_Items_Generic_Egg2(1,true));
                }
                $placed2 = $num - $left;

                //Place egg3
                shuffle($locations);
                $left = $num = mt_rand(0, floor(count($locations)/10));
                foreach ($locations as $key) if ($left > 0) {
                    $left -= 1;
                    $game->location($key)->inventory()->add(new Model_Items_Generic_Egg3(1,true));
                }

                $placed3 = $num - $left;

                //Place egg0
                foreach ($locations as $key)
                    if (!$game->location($key)->inventory()->get('Model_Items_Abstract_Easteregg'))
                        $game->location($key)->inventory()->add(new Model_Items_Generic_Egg0(1,true));

                foreach ($game->players() as $pl)
                    if ($pl->id() == $p->id()) $pl->log()->add(new Model_Log_Types_Text(null,null,'KRAAH! Danke sehr! Ich habe :e1 farbige, :e2 prächtige und :e3 Designer-Eier für dich versteckt. Viel Spaß beim Suchen, KRAHRAH!', array(':e1' => $placed1,':e2' => $placed2,':e3' => $placed3)));
                    else $pl->log()->add(new Model_Log_Types_Text(null,null,'Du hörst ein lautes Krähen in der Ferne...'));
            }
        };

        if (Tool_Events::current($game->next_tick()) == 'easter' && !$game->setting_mode(2000))
            $ret['easter'] = Model_Npc::factory()->name('Corax der Osterrabe')
                ->add_action('Info & Regeln', Model_Action::factory()
                        ->effect(Model_Effect::factory()
                                ->message('KRAAH! Ich bin Corax, der Osterrabe. Ich verstecke Ostereier und fresse die Leber von Leuten, die ich nicht leiden kann. KRAAH! Wenn du an meiner lustigen Ostereiersuche teilnehmen willst, bezahle mich mit einem Ticket oder einem Stück Leber!')
                        )
                )
                ->add_action('Mit Ticket zahlen', Model_Action::factory()
                        ->requirement('Model_Items_Generic_Ticket', 1)
                        ->effect(Model_Effect::factory()
                                ->custom($item_gen)
                        )
                )
                ->add_action('Mit Leber zahlen', Model_Action::factory()
                        ->effect(Model_Effect::factory()
                                ->buff('Model_Buffs_Blood')
                                ->effect(Model_Player::MP_STAT_HEALTH, -95)
                                ->causeofdeath("Aggressiver Rabe")
                                ->achieve(Model_Achievement::MA_MASOCHIST)
                                ->custom($item_gen)
                                ->custom(function($p) {
                                    /** @var $p Model_Player */
                                    if (!$p->alive()) $p->achievements()->achieve(Model_Achievement::MA_RAVEN);
                                }, Model_Effect::CFUNC_PROCESS_POST)
                        )
                )
                ->add_action('Gesammelte Eier eintauschen', Model_Action::factory()
                        ->effect(Model_Effect::factory()
                                ->custom(function($p) {
                                    global $user;

                                    /** @var $p Model_Player */
                                    $e1 = Tool_Scripts::count_available_items('Model_Items_Generic_Egg1', true, false, false, $p);
                                    $e2 = Tool_Scripts::count_available_items('Model_Items_Generic_Egg2', true, false, false, $p);
                                    $e3 = Tool_Scripts::count_available_items('Model_Items_Generic_Egg3', true, false, false, $p);
                                    if (Tool_Scripts::consume_available_items(array('Model_Items_Generic_Egg1' => $e1, 'Model_Items_Generic_Egg2' => $e2, 'Model_Items_Generic_Egg3' => $e3), true, false, false, $p)) {
                                        $c = floor($e1 * Model_Items_Generic_Egg1::getValue() + $e2 * Model_Items_Generic_Egg2::getValue() + $e3 * Model_Items_Generic_Egg3::getValue());
                                        if ($c > 0) {
                                            $user->award_universal_soulpoints($p->id(), $c);
                                            $p->log()->add(new Model_Log_Types_Text(null,null,'KRAAAAH! Glückwunsch! Für die Eier, die du gesammelt hast, bekommst du :num universelle Seelenpunkte! KRARAH!', array(':num' => $c)));
                                        } else $p->log()->add(new Model_Log_Types_Text(null,null,'KRARAHAHAHA! Zu schade! Du hast nicht genug Eier gesammelt um Punkte zu bekommen! KRAAH!'));
                                    }
                                })
                        )
                )
            ;
        return $ret;
    }

}	