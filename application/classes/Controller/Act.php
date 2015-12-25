<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Act extends Controller_Game {

    /**
     * @param string $action
     * @param int[] $items
     * @param Model_Player $p
     */
    private function inventory_take_drop($action, $items, $p) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         * @var Model_Items_Abstract_Item $item
         */
        global $game, $player;
        foreach ($items as $itemid)
            if ($action == 'drop') {
                if (!($item = $game->uin()->get($itemid, 'Model_Items_Abstract_Item'))) continue;
                if (!$item->drop()) continue;
                if (!$p->inventory()->remove($itemid)) continue;
                if (!$p->location()->inventory()->add($item)) $p->inventory()->add($item);
            } elseif ($action == 'take') {
                if (!($item = $game->uin()->get($itemid, 'Model_Items_Abstract_Item'))) continue;

                if (Tool_System::instance_of($item, 'Model_Items_Abstract_Ammo') && $p->id() != $player->id()) {
                    /** @var $item Model_Items_Abstract_Ammo */
                    if (!$item->remoteTake($p->id(), count($items) > 1)) continue;
                } else
                    if (!$item->take(count($items) > 1)) continue;

                if (!$p->location()->inventory()->remove($itemid)) continue;
                if (!$p->inventory()->add($item)) $p->location()->inventory()->add($item);

                if (Tool_System::instance_of($item, 'Model_Items_Abstract_Equipable')) {
                    /** @var Model_Items_Abstract_Equipable $item */
                    if (!$player->get_equipment($item->get_equipment_type()))
                        $item->equip($player);

                    if (Tool_System::instance_of($item, 'Interface_Static'))

                        foreach ($player->inventory()->get(get_class($item)) as $ep)
                            /** @var Model_Items_Abstract_Equipable $ep */
                            if ($ep->is_equipped()) {
                                $item->equip($player);
                                break;
                            }

                }
            }
    }

    private function inventory_fill($items) {
        /**
         * @global Model_Game $game
         * @var Model_Items_Abstract_Item $item
         */
        global $game;

        $bottle = false;
        $water = [];

        if (count($items) < 2) return;

        foreach ($items as $itemid) {
            if (!($item = $game->uin()->get($itemid, 'Model_Items_Abstract_Item'))) continue;

            if (Tool_System::instance_of($item, 'Interface_Fillable') || Tool_System::instance_of($item, 'Model_Items_Abstract_Bottle')) {
                if (!$bottle) $bottle = $item;
                else return;
                continue;
            }

            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Liquid')) {
                $water[] = $item;
                continue;
            }

            return;
        }

        if (!$bottle || !$water) return;

        /**
         * @var Model_Items_Abstract_Bottle $bottle
         * @var Model_Items_Abstract_Liquid $water_item
         */
        foreach ($water as $water_item)
            $bottle->interaction_fill($water_item);

    }

    private function inventory_defill($items) {
        /**
         * @global Model_Game $game
         * @var Model_Items_Abstract_Item $item
         */
        global $game;

        if (count($items) != 2) return;

        if (!($target = $game->uin()->get($items[0], 'Model_Items_Abstract_Item'))) return;
        if (!($source = $game->uin()->get($items[1], 'Model_Items_Abstract_Bottle'))) return;

        if (!(Tool_System::instance_of($target, 'Interface_Fillable') || Tool_System::instance_of($target, 'Model_Items_Abstract_Bottle'))) return;

        /**
         * @var Model_Items_Abstract_Bottle|Interface_Fillable $target
         * @var Model_Items_Abstract_Bottle $source
         */
        $target->interaction_fillfrom($source);
        return;

    }

    private function inventory_mix($items) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        if (count($items) != 2) return;

        /** @var Model_Items_Chem $chem */
        $chem = null;
        /** @var Model_Items_Abstract_Item $other_item */
        $other_item = null;
        if ($items[0] != $items[1])
            foreach ($items as $itemid) {
                if (!($item = $game->uin()->get($itemid, 'Model_Items_Abstract_Item'))) return;

                if (!$chem && Tool_System::instance_of($item, 'Model_Items_Chem'))
                    $chem = $item;
                else $other_item = $item;
            }
        else {
            if (!($chem = $game->uin()->get($items[0], 'Model_Items_Chem'))) return;
            foreach (Tool_Scripts::available_items('Model_Items_Chem') as $potential)
                /** @var Model_Items_Chem $potential */
                if ($potential->chem_value() == $chem->chem_value() && $chem->uin() != $potential->uin()) {
                    $other_item = $potential;
                    break;
                }
        }

        if (!$chem || !$other_item) return;

        $v = $chem->chem_value();
        $chem->consume();

        if ($other_item->mixchem($v)) $player->achievements()->achieve(Model_Achievement::MA_SCIENCE);
        else $player->achievements()->achieve(Model_Achievement::MA_NOSCIENCE);
    }

    private function inventory_spill($items) {
        /**
         * @global Model_Game $game
         * @var Model_Items_Abstract_Item $item
         */
        global $game;

        if (count($items) != 1) return;

        /** @var Model_Items_Abstract_Bottle $item */
        if (!($item = $game->uin()->get($items[0], 'Model_Items_Abstract_Bottle'))) return;

        $item->interaction_extract();
    }

    private function inventory_pill($action, $items, $count) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        if (count($items) != 1) return;

        /** @var Model_Items_Abstract_Pillbox $pillbox */
        if (!($pillbox = $game->uin()->get($items[0], 'Model_Items_Abstract_Pillbox'))) return;

        if ($action == 'pilltake') {
            $before = $pillbox->count();
            $pillbox->merge();
            if ($before == $pillbox->count())
                $player->log()->add('Hier liegen keine weiteren Kapseln, die du in diese Schachtel legen könntest.');
            elseif ($pillbox->is_stack_full())
                $player->log()->add('Mit all den anderen Kapseln konntest du diese Schachtel füllen. Sie enthält nun :max Kapseln.', array(':max' => $pillbox->stack_max_size()));
            else
                $player->log()->add( 'Du sammelst alle Kapseln die du dabei hast in dieser Schachtel. Sie ist zwar nicht voll, enthält nun aber immerhin :num Kapseln.', array(':num' => $pillbox->count()));
        } elseif ($action == 'pilldrop') {
            if ($count <= 0 || $count >= $pillbox->count()) return;

            $pillbox->consume($count);
            $s = get_class($pillbox);
            $player->location()->inventory()->add(new $s($count));

            if ($count == 1) $player->log()->add('Du hast eine Kapsel aus der Verpackung genommen.');
            else $player->log()->add('Du hast :num Kapseln aus der Verpackung genommen.', array(':num' => $count));
        }
    }

    private function inventory_belt($id, $addr, $count) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;
        /** @var Model_Items_Ammobelt $belt */
        $belt = $game->uin()->get($id, 'Model_Items_Ammobelt');
        if (!$belt) return;

        foreach ($belt->contains() as $class => $c)
            if (Tool_System::getClassID($class) == $addr) {
                if (!$belt->get($class, $count))
                    $player->log()->add('Soviele hast du nicht dabei.');
                else
                    $player->location()->inventory()->add(new $class($count));
                break;
            }
    }

    private function inventory_label($id, $text) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game;
        /** @var Model_Items_Ammobelt $belt */
        $item = $game->uin()->get($id, 'Interface_Label');
        if (!$item) return;

        /** @var Interface_Label $item */
        $item->set_label($text);
    }

    private function inventory_equip($id, $action) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;
        /** @var Model_Items_Abstract_Equipable $item */
        $item = $game->uin()->get($id, 'Model_Items_Abstract_Equipable');
        if (!$item || !$player->inventory()->has($item->uin())) return;

        switch ($action) {
            case 'equip':
                $item->equip($player);
                if (Tool_System::instance_of($item, 'Interface_Static'))

                    foreach ($player->inventory()->get(get_class($item)) as $ep)
                        /** @var Model_Items_Abstract_Equipable $ep */
                        $ep->equip($player);
                break;
            case 'unequip':
                $item->unequip();
                if (Tool_System::instance_of($item, 'Interface_Static'))

                    foreach ($player->inventory()->get(get_class($item)) as $ep)
                        /** @var Model_Items_Abstract_Equipable $ep */
                        $ep->unequip();
                break;
            case 'equip_primary':
                if ($item->allows_primary() && $item->is_equipped())
                    $item->equip_primary();
        }
    }

    public function japi_inventory() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Combat_Weapon
         */
        global $game, $player;

        //Block sleeping
        if ($player->get_status()->retrieve('passout') || $player->get_status()->retrieve('fragile'))
            return;

        //Get params
        $action = $this->request->post('action');
        $items = $this->request->post('items');
        $p = Tool_Scripts::check_comrade($this->request->post('player'));
        if (!$p && $this->request->post('player'))
            return;

        if (!$p) $p = $player;

        //Check Location
        if (!$p->location()) return;

        //Check params
        if (!in_array($action, ($p->id() != $player->id()) ? ['take'] : ['take','drop','fill','defill','spill','mix','pilldrop','pilltake','belt','label','equip','unequip','equip_primary']))
            return;

        $lost = false;
        if (!count($items)) return;
        foreach ($items as $i => $iid)
            if (!$game->item_available((int)$iid)) {
                unset($items[$i]);
                $lost = true;
            }

        //Transfer items
        if (in_array($action, ['take','drop']))
            $this->inventory_take_drop($action,$items,$p);
        elseif (in_array($action, ['pilldrop','pilltake']))
            $this->inventory_pill($action,$items,(int)$this->request->post('count'));
        elseif ($action == 'fill')
            $this->inventory_fill($items);
        elseif ($action == 'defill')
            $this->inventory_defill($items);
        elseif ($action == 'spill')
            $this->inventory_spill($items);
        elseif ($action == 'mix')
            $this->inventory_mix($items);
        elseif ($action == 'belt')
            $this->inventory_belt($items[0],$this->request->post('addr'),(int)$this->request->post('count'));
        elseif ($action == 'label')
            $this->inventory_label($items[0],$this->request->post('text'));
        elseif (in_array($action, ['equip','unequip','equip_primary']))
            $this->inventory_equip($items[0], $action);

        //Message
        if ($lost) $player->log()->add('Die Aktion konnte nicht vollständig ausgeführt werden, da eines oder mehrere der ausgewählten Gegenstände nicht länger in deiner Reichweite sind.');

        $this->japi_data();
    }

    public function japi_cancel() {
        /**
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $player;

        //Block sleeping
        /** @var Model_Buffs_Abstract_Fragile $buff */
        if (!($buff = $player->get_status()->retrieve('fragile')) || !$buff->abortable())
            return $this->japi_data();
        else {
            $buff->cancel();
            return $this->japi_data();
        }
    }

    public function japi_item() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $game, $player;

        //Block sleeping
        if ($player->get_status()->retrieve('passout') || $player->get_status()->retrieve('fragile'))
            return $this->japi_data();

        //Get UIN
        $id = $this->request->post('item');
        $action = $this->request->post('action');
        if ($coid = $this->request->post('coitem')) {
            $argument = $game->uin()->get($coid, 'Model_Items_Abstract_Item');
            if (!$game->item_available($coid) || !$argument) {
                $player->log()->add('Die Aktion konnte nicht vollständig ausgeführt werden, da eines oder mehrere der ausgewählten Gegenstände nicht länger in deiner Reichweite sind.');
                return $this->japi_data();
            }
        } else $argument = $this->request->post('coarg');

        if ($side_id = $this->request->post('co')) {
            if (!Tool_Scripts::check_comrade($side_id)) {
                $player->log()->add('Dieser Spieler befindet sich nicht in deiner Nähe.');
                return $this->japi_data();
            }

            $side = $game->get_player($side_id);
        } else $side = null;

        //Get Item, or thow Exception if this UIN does not resolve to a valid item
        if (substr($id, 0, 5) === 'npc::') {
            $npc = $player->location()->get_npc(substr($id, 5));
            if (!$npc) return null;
            $npc->perform($action, $player, $side, $argument);
        } else {
            $item = $game->uin()->get((int)$id, 'Model_Items_Abstract_Item');
            if (!$game->item_available((int)$id) || !$item) {
                $player->log()->add('Die Aktion konnte nicht vollständig ausgeführt werden, da eines oder mehrere der ausgewählten Gegenstände nicht länger in deiner Reichweite sind.');
                return $this->japi_data();
            }
            $item->interact($action, $argument, $side);
        }


        return $this->japi_data();
    }

}