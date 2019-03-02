<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Epic_Raven extends Model_Items_Abstract_Virtual {

    protected static $manual_ui = true;

    private $doped = false;
    private $rest;

    public function get_inventory_size(): int
    {
        return $this->doped ? 60 : 30;
    }

    public function get_inventory_capacity(): int
    {
        return $this->doped ? 4 : 3;
    }

    public function is_doped(): bool
    {
        return $this->doped;
    }

    public function get_rest() {
        $a = $this->rest - Globals::CurrentGameF()->duration();
        return $a > 0 ? $a : false;
    }

    protected function hid(): Model_Hid {
        $hid = parent::hid();

        if (!$this->doped && !$this->get_rest())
            $hid->add_action('Dopen', Model_Action::factory()
                ->buttonskin('epic')
                ->description('Mit ein paar Steroiden (und einer Luftpumpe) kannst du deinen Raben zu einem mächtigen Greifen aufpumpen, der auch schwere Gegenstände transportieren kann. Dies erhöht allerdings auch die Erhohlungszeit.')
                ->requirement(Model_Items_Paracetin::cls(), 3)
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
                        /** @var Model_Player $p */
                        if (!is_array($a)) return;

                        list($arg,$arg2) = $a;
                        $arg =  max(0,min((int)$arg, 2));
                        $arg2 = max(1,min((int)$arg2,2));

                        switch ($arg) {
                            case 0: $a = [0,15]; break;
                            case 1: $a = [16,50]; break;
                            case 2: $a = [51,PHP_INT_MAX]; break;
                            default: $a = [-1,-1];
                        }

                        $max_weight = $this->get_inventory_size();
                        $max_capacity = $this->get_inventory_capacity();


                        $current_location = $p->location_class();
                        $locations = array_filter(Globals::CurrentGameF()->main_map()->build_route_array($current_location), function($location) use ($current_location, $a) {
                            if ($location['id'] === $current_location || $a[0] > $location['distance'] || $a[1] < $location['distance']) return false;
                            $obj = Globals::CurrentGameF()->location($location['id']);
                            if (!$obj || Tool_System::instance_of($obj, ['Model_Places_Abstract_Hideout', 'Model_Places_Abstract_Node'])) return false;
                            return true;
                        });

                        if (!$locations) {
                            $p->log()->add('In dem gewählten Gebiet scheint es keine Ziele für den Raben zu geben...');
                            return;
                        }

                        switch ($arg2) {
                            case 1:
                                $req = Struct_ItemEntry::make(Model_Items_Basefood::cls(), max(1,2*$arg));
                                break;
                            case 2:
                                $req = Struct_ItemEntry::make(Model_Items_Rawmeat::cls(), max(1,1+$arg));
                                break;
                            default: return;
                        }
                        if (!Tool_Scripts::consume_items([$req], Struct_ScriptItemSource::default()->use_perspective($p))) {
                            $p->log()->add('Raben sind keine sonderlich altruistisch eingestellten Tiere... du musst ihn schon ausreichend füttern, wenn du Gegenstände von ihm bekommen möchtest.');
                            return;
                        }

                        $location = Globals::CurrentGameF()->locationF(Tool_Gambling::select(array_keys($locations)));

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

                        $weight = 0;
                        $final = [];
                        $final_ids = [];
                        shuffle($items);
                        foreach (array_merge($items,$location->inventory()->get()) as $item)
                            /** @var Model_Items_Abstract_Item $item */
                            if (!in_array($item->uin(), $final_ids, true)) {
                                $msg = '';
                                if ($weight >= $max_weight || count($final) >= $max_capacity) break;
                                if ($item->weight() + $weight < $max_weight && (Tool_System::instance_of($item, Model_Items_Abstract_Ammo::cls()) || $item->can_take($msg))) {
                                    $final[] = $item;
                                    $final_ids[] = $item->uin();
                                    $weight += $item->weight();
                                    $location->inventory()->remove($item->uin());
                                }
                            }

                        $p->location()->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, 'Corax der Rabe'));
                        if (count($final)) {
                            $p->log()->add('Der Ausflug deines Raben scheint erfolgreich verlaufen zu sein! Er hat :location besucht und dir sogar etwas mitgebracht.', [], [':location' => $location->name()]);
                            $p->location()->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_RAVEN, $final, 'Corax der Rabe'));
                            foreach ($final as $item) {
                                $item->drop();
                                $p->location()->inventory()->add($item);
                            }
                        }
                        elseif (!count($final) && count($items))
                            $p->log()->add('Dein Rabe hat :location besucht und dort auch etwas gefunden, konnte es jedoch nicht hierher tragen...',[],[':location' => $location->name()]);
                        else $p->log()->add('Dein Rabe hat :location besucht, ist jedoch mit leeren Krallen zurückgekehrt...',[],[':location' => $location->name()]);

                        $this->rest = Globals::CurrentGameF()->duration() + ($this->doped ? 24 : 12);
                        $this->doped = false;
                    })
                )
                , 'fetch');


        return $hid;
    }
}	