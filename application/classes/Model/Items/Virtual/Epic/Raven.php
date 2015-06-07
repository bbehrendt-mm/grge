<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Epic_Raven extends Model_Items_Abstract_Virtual {

    protected static $manual_ui = true;

    private $doped = false;
    private $rest;

    public function get_inventory_size() {
        return $this->doped ? 60 : 30;
    }

    public function get_inventory_capacity() {
        return $this->doped ? 4 : 3;
    }

    public function is_doped() {
        return $this->doped;
    }

    public function get_rest() {
        /** @global Model_Game $game */
        global $game;

        $a = $this->rest - $game->duration();
        return $a > 0 ? $a : false;
    }

    protected function hid() {
        /** @global Model_Game $game */
        global $game;

        $hid = parent::hid();

        if (!$this->doped && !$this->get_rest())
            $hid->add_action('Dopen', Model_Action::factory()
                ->buttonskin('epic')
                ->description('Mit ein paar Steroiden (und einer Luftpumpe) kannst du deinen Raben zu einem mächtigen Greifen aufpumpen, der auch schwere Gegenstände transportieren kann. Dies erhöht allerdings auch die Erhohlungszeit.')
                ->requirement('Model_Items_Paracetin', 3)
                ->effect(Model_Effect::factory()
                    ->message('Du hast deinem Raben ein paar Pillen in seine Körner gemischt.')
                    ->custom(function() {
                        $this->doped = true;
                    })
                )
            );

        if (!$this->get_rest())
            $hid->add_action('Raben aussenden', Model_Action::factory()
                ->buttonskin('epic')
                ->flag('as','fetch')
                ->effect(Model_Effect::factory()
                    ->custom(function($p, $a) {
                        /** @global Model_Game $game */
                        global $game;

                        /** @var Model_Player $p */
                        $arg = max(0,min((int)$a,2));

                        switch ($arg) {
                            case 0: $a = [0,15]; break;
                            case 1: $a = [16,50]; break;
                            case 2: $a = [51,PHP_INT_MAX]; break;
                            default: $a = [-1,-1];
                        }

                        $max_weight = $this->get_inventory_size();
                        $max_capacity = $this->get_inventory_capacity();


                        $current_location = $p->location_class();
                        $locations = array_filter($game->map()->build_route_array($current_location), function($location) use ($current_location, $a) {
                            /** @global Model_Game $game */
                            global $game;

                            if ($location['id'] == $current_location || $a[0] > $location['distance'] || $a[1] < $location['distance']) return false;
                            $obj = $game->location($location['id']);
                            if (!$obj || Tool_System::instance_of($obj, ['Model_Places_Abstract_Hideout', 'Model_Places_Abstract_Node'])) return false;
                            return true;
                        });

                        if (!$locations) {
                            $p->log()->add('In dem gewählten Gebiet scheint es keine Ziele für den Raben zu geben...');
                            return;
                        }

                        if (!Tool_Scripts::consume_available_items(['Model_Items_Basefood' => max(1,2*$arg)], true, true, false, $p)) {
                            $p->log()->add('Raben sind keine sonderlich altruistisch eingestellten Tiere... du musst ihn schon ausreichend füttern, wenn du Gegenstände von ihm bekommen möchtest.');
                            return;
                        }

                        $location = $game->location(Tool_Gambling::select(array_keys($locations)));

                        $p->location()->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, 'Corax der Rabe'));
                        $location->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, 'Corax der Rabe'));
                        $items = [];
                        for ($i = 0; $i < 3; $i++) {
                            if ($item = $location->find_item(false, true)) {
                                $items[] = $item;
                                $location->inventory()->add($item);
                            }
                        }
                        if ($items) $location->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_DIGUP, $items, 'Corax der Rabe'));
                        $location->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, 'Corax der Rabe'));

                        $weight = 0;;
                        $final = [];
                        shuffle($items);
                        foreach (array_merge($items,$location->inventory()->get()) as $item) {
                            /** @var Model_Items_Abstract_Item $item */
                            if (count($final) >= $max_capacity || $weight >= $max_weight) break;
                            if ($item->weight() + $weight < $max_weight) {
                                $final[] = $item;
                                $weight += $item->weight();
                                $location->inventory()->remove($item->uin());
                            }
                        }

                        if (count($final)) {
                            $p->log()->add('Der Ausflug deines Raben scheint erfolgreich verlaufen zu sein! Er hat :location besucht und dir sogar etwas mitgebracht.', [], [':location' => $location->name()]);
                            $p->location()->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_RAVEN, $final, 'Corax der Rabe'));
                            foreach ($final as $item) $p->location()->inventory()->add($item);
                        }
                        elseif (!count($final) && count($items))
                            $p->log()->add('Dein Rabe hat :location besucht und dort auch etwas gefunden, konnte es jedoch nicht hierher tragen...',[],[':location' => $location->name()]);
                        else $p->log()->add('Dein Rabe hat :location besucht, ist jedoch mit leeren Krallen zurückgekehrt...',[],[':location' => $location->name()]);

                        $p->location()->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, 'Corax der Rabe'));

                        $this->doped = false;
                        $this->rest = $game->duration() + ($this->doped ? 24 : 12);
                    })
                )
                , 'fetch');


        return $hid;
    }
}	