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
                if (!$item->drop($p)) continue;
                if (!$p->inventory()->remove($itemid)) continue;
                if (!$p->location()->inventory()->add($item)) $p->inventory()->add($item);
                else $p->location()->log()->add(new Model_Log_Types_Transaction(Model_Log_Types_Transaction::MLTT_DOWN, $item, $p->id()));
            } elseif ($action == 'take') {
                if (!($item = $game->uin()->get($itemid, 'Model_Items_Abstract_Item'))) continue;

                if (Tool_System::instance_of($item, 'Model_Items_Abstract_Ammo')) {
                    /** @var $belt Model_Items_Ammobelt */
                    $belt = Tool_Scripts::first_available_item(Model_Items_Ammobelt::cls(), true, false, false, $p);
                    if (!$belt) {
                        if (count($items) <= 1) $player->log()->add($p->id() == $player->id() ? 'Du benötigst einen Munitionsgürtel, um diesen Gegenstand mitführen zu können.' : 'Dein Freund benötigt einen Munitionsgürtel, um diesen Gegenstand mitführen zu können.');
                        continue;
                    }

                    /** @var Model_Items_Abstract_Ammo $item */
                    if ($item->take(count($items) > 1)) {
                        $p->location()->log()->add(new Model_Log_Types_Transaction(Model_Log_Types_Transaction::MLTT_UP, $item, $p->id()));
                        $belt->add($item);
                    }

                } else {
                    if (!$item->take(count($items) > 1)) continue;
                    if (!$p->location()->inventory()->remove($itemid)) continue;
                    if (!$p->inventory()->add($item)) $p->location()->inventory()->add($item);
                    else
                        $p->location()->log()->add(new Model_Log_Types_Transaction(Model_Log_Types_Transaction::MLTT_UP, $item, $p->id()));
                        if (Tool_System::instance_of($item, 'Model_Items_Abstract_Equipable') && !Tool_Scripts::is_npc($p)) {
                            /** @var Model_Items_Abstract_Equipable $item */
                            if (!$p->get_equipment($item->get_equipment_type()))
                                $item->equip($p);

                            if (Tool_System::instance_of($item, 'Interface_Static'))

                                foreach ($p->inventory()->get(get_class($item)) as $ep)
                                    /** @var Model_Items_Abstract_Equipable $ep */
                                    if ($ep->is_equipped()) {
                                        $item->equip($p);
                                        break;
                                    }

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

    private function inventory_spill($items, $all = false) {
        /**
         * @global Model_Game $game
         * @var Model_Items_Abstract_Item $item
         */
        global $game;

        if (count($items) != 1) return;

        /** @var Model_Items_Abstract_Bottle $item */
        if (!($item = $game->uin()->get($items[0], 'Model_Items_Abstract_Bottle'))) return;

        $item->interaction_extract($all);
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
                    $player->location()->inventory()->add(new $class($count, true));
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
                        if ($ep->uin() != $item->uin())
                            $ep->equip($player);
                break;
            case 'unequip':
                $item->unequip();
                if (Tool_System::instance_of($item, 'Interface_Static'))

                    foreach ($player->inventory()->get(get_class($item)) as $ep)
                        /** @var Model_Items_Abstract_Equipable $ep */
                        if ($ep->uin() != $item->uin())
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
        $action = $this->post('action');
        $items = $this->post('items');
        $p = Tool_Scripts::check_comrade($this->post('player'));
        if (!$p && $this->post('player'))
            return;

        if (!$p) $p = $player;

        //Check Location
        if (!$p->location()) return;

        //Check params
        if (!in_array($action, ['take','drop','fill','defill','spill','mix','pilldrop','pilltake','belt','label','equip','unequip','equip_primary']))
            return;

        // Remote player
        if ($p->id() != $player->id())
            if (!$p->allow(Interface_Plentity::IC_ALLOW_SHOW_INVENTORY))
                return;
            else switch ($action) {
                case 'take': case 'pilltake': case 'fill': if (!$p->allow(Interface_Plentity::IC_ALLOW_ITEM_PICKUP)) return; break;
                case 'drop': case 'pilldrop': case 'belt': case 'spill': if (!$p->allow(Interface_Plentity::IC_ALLOW_ITEM_DROP)) return; break;
                case 'label': case 'mix': case 'defill': if (!$p->allow(Interface_Plentity::IC_ALLOW_ITEMS_USE)) return; break;
                case 'equip': case 'unequip': case 'equip_primary': default: return;
            }

        $lost = false;
        if (!$items) $items = [];
        foreach ($items as $i => $iid)
            if (!$game->item_available((int)$iid, $p)) {
                unset($items[$i]);
                $lost = true;
            }
        if (!count($items)) return;

        //Transfer items
        if (in_array($action, ['take','drop']))
            $this->inventory_take_drop($action,$items,$p);
        elseif (in_array($action, ['pilldrop','pilltake']))
            $this->inventory_pill($action,$items,(int)$this->post('count'));
        elseif ($action == 'fill')
            $this->inventory_fill($items);
        elseif ($action == 'defill')
            $this->inventory_defill($items);
        elseif ($action == 'spill')
            $this->inventory_spill($items, (bool)$this->post('all'));
        elseif ($action == 'mix')
            $this->inventory_mix($items);
        elseif ($action == 'belt')
            $this->inventory_belt($items[0],$this->post('addr'),(int)$this->post('count'));
        elseif ($action == 'label')
            $this->inventory_label($items[0],$this->post('text'));
        elseif (in_array($action, ['equip','unequip','equip_primary']))
            $this->inventory_equip($items[0], $action);

        //Message
        if ($lost) $player->log()->add('Die Aktion konnte nicht vollständig ausgeführt werden, da eines oder mehrere der ausgewählten Gegenstände nicht länger in deiner Reichweite sind.');

        $this->japi_data();
    }

    public function japi_cancel() {
        /**
         * @global $player Model_Player
         * @global $game Model_Game
         * @var $item Model_Items_Abstract_Item
         */
        global $player, $game;

        if ((($pid = $this->post('p')) && ($p = $game->get_player($pid)))) {
            if (!$p->allow(Interface_Plentity::IC_ALLOW_MANAGE_ACTIVITY)) return $this->japi_data();
        } else $p = $player;

        /** @var Model_Buffs_Abstract_Fragile $buff */
        if (!($buff = $p->get_status()->retrieve('fragile')) || !$buff->abortable())
            return $this->japi_data();
        else {
            $buff->cancel();
            return $this->japi_data();
        }
    }

    public static function code_item($id, $action, $side_id = null, $argument = null, $user = null) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $game, $player;

        //Block sleeping
        if ($player->get_status()->retrieve('passout') || $player->get_status()->retrieve('fragile'))
            return;

        $user = $user ? $game->get_player($user) : null;

        if ($side_id) {
            if ($user && $side_id) return;

            $side_player = $game->get_player($side_id);

            if (!$side_player || !Tool_Scripts::check_comrade($side_id) || !$side_player->allow(Interface_Plentity::IC_ALLOW_ITEMS_SIDEUSE)) {
                $player->log()->add('Du kannst aktuell keine Gegenstände auf diesem Spieler anwenden.');
                return;
            }

            $side = $game->get_player($side_id);
        } else $side = null;

        //Block sleeping (again)
        if ($user && (!$user->allow(Interface_Plentity::IC_ALLOW_ITEMS_USE) || $user->get_status()->retrieve('passout') || $user->get_status()->retrieve('fragile'))) {
            $player->log()->add('Dieser Spieler kann aktuell keinen Gegenstand verwenden.');
            return;
        }

        //Get Item, or throw Exception if this UIN does not resolve to a valid item
        $item = $game->uin()->get((int)$id, 'Model_Items_Abstract_Item');
        if (!$game->item_available((int)$id, $user ? $user : null) || !$item) {
            $player->log()->add('Die Aktion konnte nicht vollständig ausgeführt werden, da eines oder mehrere der ausgewählten Gegenstände nicht länger in deiner Reichweite sind.');
            return;
        }

        // Prevent the remote use of Virtual Items
        if ($user && Tool_System::instance_of($item, Model_Items_Abstract_Virtual::cls()))
            return;

        if ($user) $user->item_preaction($item,$action);
        if ($r = $item->interact($action, $user ? $user : $player, $argument, $side))
            $player->location()->log()->add(new Model_Log_Types_Transaction(Model_Log_Types_Transaction::MLTT_USE, $item, $user ? $user->id() : $player->id(), $item->resolve_action($action, $user ? $user : $player)));

        if ($user) {
            $user->item_reaction();
            $player->log()->add($r ? ':name hat deinen Befehl befolgt und :item eingesetzt!' : ':name konnte :item nicht einsetzen...', [':name' => $user->name()], [':item' => $item->name()]);
        }

    }

    public static function code_npc($id, $action, $side_id = null, $argument = null, $user = null) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $game, $player;

        //Block sleeping
        if ($player->get_status()->retrieve('passout') || $player->get_status()->retrieve('fragile'))
            return;

        $user = $user ? $game->get_player($user) : null;

        if ($side_id) {
            if ($user && $side_id) return;

            $side_player = $game->get_player($side_id);

            if (!$side_player || !Tool_Scripts::check_comrade($side_id) || !$side_player->allow(Interface_Plentity::IC_ALLOW_ITEMS_SIDEUSE)) {
                $player->log()->add('Du kannst aktuell keine Gegenstände auf diesem Spieler anwenden.');
                return;
            }

            $side = $game->get_player($side_id);
        } else $side = null;

        //Block sleeping (again)
        if ($user && (!$user->allow(Interface_Plentity::IC_ALLOW_ITEMS_USE) || $user->get_status()->retrieve('passout') || $user->get_status()->retrieve('fragile'))) {
            $player->log()->add('Dieser Spieler kann aktuell keinen Gegenstand verwenden.');
            return;
        }

        //Get NPC, or throw Exception if this UIN does not resolve to a valid npc or the npc is at a different location
        $npc = $game->get_npc($id);
        if (!$npc || ($npc->location_class() != $player->location_class())) {
            $player->log()->add('Die Aktion konnte nicht ausgeführt werden, da der gewählte NPC außerhalb deiner Reichweite ist.');
            return;
        }

        $hid = $npc->hid();
        $player->get_status()->set_cause_of_death("Vergiftung");
        if ($hid->can($action))
            $hid->perform($action, $player, $side, $argument);
        $player->get_status()->clear_cause_of_death();
    }

    public function japi_item() {
        $id = $this->post('item');
        if (strpos($id, 'npc//') === 0)
            static::code_npc(substr($id, 5), $this->post('action'), $this->post('co'), $this->post('coarg'), $this->post('player'));
        else static::code_item($id, $this->post('action'), $this->post('co'), $this->post('coarg'), $this->post('player'));
        return $this->japi_data();
    }

}