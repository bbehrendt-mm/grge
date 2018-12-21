<?php

class Model_NPC_Event_Crow extends Model_NPC_Humanoid
{
    protected static $buffs_heartbeat_name = 'Model_Buffs_Event_Fakeheart';
    protected static $buffs_metabolism_name = 'Model_Buffs_Event_Fakemetabolism';
    protected static $buffs_place_various = false;

    protected static $handle_death = false;

    public function __construct($name = null) {
        parent::__construct('Corax der Osterrabe');
    }

    public function entity_action() {
        return 'Verbreitet Osterstimmung';
    }

    public function entity_description() {
        return 'KRAAH! Ich bin Corax, der Osterrabe. Ich verstecke Ostereier und fresse die Leber von Leuten, die ich nicht leiden kann. KRAAH! Wenn du an meiner lustigen Ostereiersuche teilnehmen willst, bezahle mich mit einem Ticket oder einem Stück Leber!';
    }

    public function is_fighter() {
        return false;
    }

    public function kill() {
        if ($this->location()) $this->location()->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, $this->id(), true));
        parent::kill();
    }

    public function hid(): Model_Hid
    {
        $item_gen = function($p)  {
            /** @var $p Model_Player */
            $locations = Globals::CurrentGameF()->main_map()->build_route_array($p->location()->uin());
            foreach ($locations as $key => $data) {
                if (Tool_Scripts::location_type($key) !== 0)
                    unset($locations[$key]);
                elseif (count(Globals::CurrentGameF()->locationF($key)->inventory()->get(Model_Items_Abstract_Easteregg::cls())) > 0)
                    unset($locations[$key]);
            }
            $locations = array_keys($locations);

            if (count($locations) < 2) {
                $p->log()->add(new Model_Log_Types_String(null,'KRAAHAHAHA! So ein Pech! Du kennst nicht genügend Orte! KRARAAAAH!'));
                return;
            }

            //Place egg1
            shuffle($locations);
            $left = $num = random_int(count($locations), 3*count($locations));
            foreach ($locations as $key) {
                $put = random_int(min($left, ceil(count($locations)/5)), min($left, ceil(count($locations)/2)));
                $left -= $put;
                for ($i = 0; $i < $put; $i++)
                    Globals::CurrentGameF()->locationF($key)->inventory()->add(new Model_Items_Generic_Egg1(1,true));
            }
            $placed1 = $num - $left;

            //Place egg2
            shuffle($locations);
            $left = $num = random_int(0, floor(count($locations)/4));
            foreach ($locations as $key) if ($left > 0) {
                --$left;
                Globals::CurrentGameF()->locationF($key)->inventory()->add(new Model_Items_Generic_Egg2(1,true));
            }
            $placed2 = $num - $left;

            //Place egg3
            shuffle($locations);
            $left = $num = random_int(0, floor(count($locations)/10));
            foreach ($locations as $key) if ($left > 0) {
                --$left;
                Globals::CurrentGameF()->locationF($key)->inventory()->add(new Model_Items_Generic_Egg3(1,true));
            }

            $placed3 = $num - $left;

            //Place egg0
            foreach ($locations as $key)
                if (!Globals::CurrentGameF()->locationF($key)->inventory()->get(Model_Items_Abstract_Easteregg::cls()))
                    Globals::CurrentGameF()->locationF($key)->inventory()->add(new Model_Items_Generic_Egg0(1,true));

            foreach (Globals::CurrentGameF()->players() as $pl)
                if ($pl->id() === $p->id()) $pl->log()->add(new Model_Log_Types_String(null,'KRAAH! Danke sehr! Ich habe :e1 farbige, :e2 prächtige und :e3 Designer-Eier für dich versteckt. Viel Spaß beim Suchen, KRAHRAH!', array(':e1' => $placed1,':e2' => $placed2,':e3' => $placed3)));
                else $pl->log()->add(new Model_Log_Types_String(null,'Du hörst ein lautes Krähen in der Ferne...'));
        };

        return parent::hid()
            ->add_action('Mit Ticket zahlen', Model_Action::factory()
                ->requirement(Model_Items_Generic_Ticket::cls(), 1)
                ->effect(Model_Effect::factory()
                     ->custom($item_gen)
                )
            )
            ->add_action('Mit Leber zahlen', Model_Action::factory()
                ->effect(Model_Effect::factory()
                     ->buff('Model_Buffs_Blood')
                     ->effect(Model_Status::MS_STAT_HEALTH, -95)
                     ->causeofdeath('Aggressiver Rabe')
                     ->achieve(Model_Achievement::MA_MASOCHIST)
                     ->custom($item_gen)
                     ->custom(function(Model_Player $p) {
                         if (!$p->get_status()->alive()) $p->achievements()->achieve(Model_Achievement::MA_RAVEN);
                     }, Model_Effect::CFUNC_PROCESS_POST)
                )
            )
            ->add_action('Gesammelte Eier eintauschen', Model_Action::factory()
                ->effect(Model_Effect::factory()
                     ->custom(function(Model_Player $p) {
                         [$e1,$e2,$e3] = Tool_Scripts::count_item_matrix(
                             [Model_Items_Generic_Egg1::cls(), Model_Items_Generic_Egg2::cls(), Model_Items_Generic_Egg3::cls()],
                             Struct_ScriptItemSource::onlyPlayer()->use_perspective($p)
                         );

                         if (Tool_Scripts::consume_items(Struct_ItemEntry::convert( [
                                Model_Items_Generic_Egg1::cls() => $e1,
                                Model_Items_Generic_Egg2::cls() => $e2,
                                Model_Items_Generic_Egg3::cls() => $e3
                             ] ), Struct_ScriptItemSource::onlyPlayer()->use_perspective($p))) {

                                $c = floor($e1 * Model_Items_Generic_Egg1::getValue() + $e2 * Model_Items_Generic_Egg2::getValue() + $e3 * Model_Items_Generic_Egg3::getValue());
                                if ($c > 0) {
                                    Globals::CurrentUserF()->award_coins($p->id(), $c);
                                    $p->log()->add(new Model_Log_Types_String(null,'KRAAAAH! Glückwunsch! Für die Eier, die du gesammelt hast, bekommst du :num BrainCoins! KRARAH!', array(':num' => $c)));
                                } else $p->log()->add(new Model_Log_Types_String(null,'KRARAHAHAHA! Zu schade! Du hast nicht genug Eier gesammelt um Punkte zu bekommen! KRAAH!'));
                         }
                     })
                )
            )
            ;
    }
}