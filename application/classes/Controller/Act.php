<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Act extends Controller_Game {

    /**
     * @param string       $action
     * @param int[]        $items
     * @param Model_Player $p
     *
     * @throws Exception
     */
    private function inventory_take_drop($action, $items, $p): void
    {
        $inventory_full_message = false;

        /**
         * @var Model_Items_Abstract_Item $item
         */
        foreach ($items as $itemid)
            if ($action === 'drop') {
                if (!($item = Globals::CurrentGameF()->uin()->get($itemid, Model_Items_Abstract_Item::cls()))) continue;

                $message = '';
                if (!$item->can_drop($message, $p)) {
                    if (!empty($message))
                        Globals::PrimaryPlayerF()->log()->add($message);
                    else if (count($items) <= 1) Globals::PrimaryPlayerF()->log()->add($p->id() === Globals::PrimaryPlayerF()->id() ? 'Du kannst diesen Gegenstand nicht ablegen.' : 'Dein Freund kann diesen Gegenstand nicht ablegen.');
                    continue;
                }

                if (!$p->inventory()->remove($itemid)) continue;
                if (!$p->location()->inventory()->add($item)) {
                    $p->inventory()->add($item);
                    continue;
                }
                if (!$item->drop($p)) throw new LogicException('Inconsistent item transfer behaviour detected.');
                else $p->location()->log()->add(new Model_Log_Types_Transaction(Model_Log_Types_Transaction::MLTT_DOWN, $item, $p->id()));

            } elseif ($action === 'take') {
                if (!($item = Globals::CurrentGameF()->uin()->get($itemid, Model_Items_Abstract_Item::cls()))) continue;

                $message = '';
                if (!$item->can_take($message)) {
                    if (!empty($message))
                        Globals::PrimaryPlayerF()->log()->add($message);
                    else if (count($items) <= 1) Globals::PrimaryPlayerF()->log()->add($p->id() === Globals::PrimaryPlayerF()->id() ? 'Du kannst diesen Gegenstand nicht aufheben.' : 'Dein Freund kann diesen Gegenstand nicht aufheben.');
                    continue;
                }

                if (Tool_System::instance_of($item, Model_Items_Abstract_Ammo::cls())) {
                    /** @var $belt Model_Items_Ammobelt */
                    $belt = Tool_Scripts::first_item(Model_Items_Ammobelt::cls(), Struct_ScriptItemSource::onlyPlayer()->use_perspective($p));
                    if (!$belt) {
                        if (count($items) <= 1) Globals::PrimaryPlayerF()->log()->add($p->id() === Globals::PrimaryPlayerF()->id() ? 'Du benötigst einen Munitionsgürtel, um diesen Gegenstand mitführen zu können.' : 'Dein Freund benötigt einen Munitionsgürtel, um diesen Gegenstand mitführen zu können.');
                        continue;
                    }

                    /** @var Model_Items_Abstract_Ammo $item */
                    if ($item->take()) {
                        $p->location()->log()->add(new Model_Log_Types_Transaction(Model_Log_Types_Transaction::MLTT_UP, $item, $p->id()));
                        $belt->add($item);
                    } else throw new LogicException('Inconsistent item transfer behaviour detected.');

                } else {
                    if (!$p->location()->inventory()->remove($itemid)) continue;
                    if (!$p->inventory()->add($item)) {
                        if (!$inventory_full_message) {
                            Globals::PrimaryPlayerF()->log()->add($p->id() === Globals::PrimaryPlayerF()->id() ? 'Dein Rucksack ist voll.' : 'Der Rucksack deines Freundes ist voll.');
                            $inventory_full_message = true;
                        }

                        $p->location()->inventory()->add($item);
                        continue;
                    }
                    if (!$item->take()) throw new LogicException('Inconsistent item transfer behaviour detected.');
                    else
                        $p->location()->log()->add(new Model_Log_Types_Transaction(Model_Log_Types_Transaction::MLTT_UP, $item, $p->id()));
                        if (!Tool_Scripts::is_npc($p) && Tool_System::instance_of($item, Model_Items_Abstract_Equipable::cls())) {
                            /** @var Model_Items_Abstract_Equipable $item */
                            if (!$p->get_equipment($item->get_equipment_type()))
                                $item->equip($p);

                            if (Tool_System::instance_of($item, 'Interface_Static'))

                                if ($item !== null)
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

    private function inventory_fill($items): void
    {
        /**
         * @var Model_Items_Abstract_Item $item
         */
        $bottle = false;
        $water = [];

        if (count($items) < 2) return;

        foreach ($items as $itemid) {
            if (!($item = Globals::CurrentGameF()->uin()->get($itemid, Model_Items_Abstract_Item::cls()))) continue;

            if (Tool_System::instance_of($item, 'Interface_Fillable') || Tool_System::instance_of($item, Model_Items_Abstract_Bottle::cls())) {
                if (!$bottle) $bottle = $item;
                else return;
                continue;
            }

            if (Tool_System::instance_of($item, Model_Items_Abstract_Liquid::cls())) {
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

    private function inventory_defill($items): void
    {
        /**
         * @var Model_Items_Abstract_Item $item
         */
        if (count($items) !== 2) return;

        if (!($target = Globals::CurrentGameF()->uin()->get($items[0], Model_Items_Abstract_Item::cls()))) return;
        if (!($source = Globals::CurrentGameF()->uin()->get($items[1], Model_Items_Abstract_Bottle::cls()))) return;

        if (!(Tool_System::instance_of($target, 'Interface_Fillable') || Tool_System::instance_of($target, Model_Items_Abstract_Bottle::cls()))) return;

        /**
         * @var Model_Items_Abstract_Bottle|Interface_Fillable $target
         * @var Model_Items_Abstract_Bottle $source
         */
        $target->interaction_fillfrom($source);
    }

    private function inventory_mix($items): void
    {
        if (count($items) !== 2) return;

        /** @var Model_Items_Chem $chem */
        $chem = null;
        /** @var Model_Items_Abstract_Item $other_item */
        $other_item = null;
        if ($items[0] !== $items[1])
            foreach ($items as $itemid) {
                if (!($item = Globals::CurrentGameF()->uin()->get($itemid, Model_Items_Abstract_Item::cls()))) return;

                if (!$chem && Tool_System::instance_of($item, Model_Items_Chem::cls()))
                    $chem = $item;
                else $other_item = $item;
            }
        else {
            if (!($chem = Globals::CurrentGameF()->uin()->get($items[0], Model_Items_Chem::cls()))) return;
            foreach (Tool_Scripts::get_items(Model_Items_Chem::cls()) as $potential)
                /** @var Model_Items_Chem $potential */
                if ($potential->chem_value() === $chem->chem_value() && $chem->uin() !== $potential->uin()) {
                    $other_item = $potential;
                    break;
                }
        }

        if (!$chem || !$other_item) return;

        $v = $chem->chem_value();
        $chem->consume();

        if ($other_item->mixchem($v)) Globals::PrimaryPlayerF()->achievements()->achieve(Model_Achievement::MA_SCIENCE);
        else Globals::PrimaryPlayerF()->achievements()->achieve(Model_Achievement::MA_NOSCIENCE);
    }

    private function inventory_spill($items, $all = false): void
    {
        /**
         * @var Model_Items_Abstract_Item $item
         */
        if (count($items) !== 1) return;

        /** @var Model_Items_Abstract_Bottle $item */
        if (!($item = Globals::CurrentGameF()->uin()->get($items[0], Model_Items_Abstract_Bottle::cls()))) return;

        $item->interaction_extract($all);
    }

    private function inventory_pill($action, $items, $count): void
    {
        if (count($items) !== 1) return;

        /** @var Model_Items_Abstract_Pillbox $pillbox */
        if (!($pillbox = Globals::CurrentGameF()->uin()->get($items[0], Model_Items_Abstract_Pillbox::cls()))) return;

        if ($action === 'pilltake') {
            $before = $pillbox->count();
            $pillbox->merge();
            if ($before === $pillbox->count())
                Globals::PrimaryPlayerF()->log()->add('Hier liegen keine weiteren Kapseln, die du in diese Schachtel legen könntest.');
            elseif ($pillbox->is_stack_full())
                Globals::PrimaryPlayerF()->log()->add('Mit all den anderen Kapseln konntest du diese Schachtel füllen. Sie enthält nun :max Kapseln.', array(':max' => $pillbox->stack_max_size()));
            else
                Globals::PrimaryPlayerF()->log()->add( 'Du sammelst alle Kapseln die du dabei hast in dieser Schachtel. Sie ist zwar nicht voll, enthält nun aber immerhin :num Kapseln.', array(':num' => $pillbox->count()));
        } elseif ($action === 'pilldrop') {
            if ($count <= 0 || $count >= $pillbox->count()) return;

            $pillbox->consume($count);
            $s = get_class($pillbox);
            Globals::PrimaryPlayerF()->location()->inventory()->add(new $s($count));

            if ($count === 1) Globals::PrimaryPlayerF()->log()->add('Du hast eine Kapsel aus der Verpackung genommen.');
            else Globals::PrimaryPlayerF()->log()->add('Du hast :num Kapseln aus der Verpackung genommen.', array(':num' => $count));
        }
    }

    private function inventory_belt($id, $addr, $count): void
    {
        /** @var Model_Items_Ammobelt $belt */
        $belt = Globals::CurrentGameF()->uin()->get($id, Model_Items_Ammobelt::cls());
        if (!$belt) return;

        foreach ($belt->contains() as $class => $c)
            if (Tool_System::getClassID($class) === $addr) {
                if (!$belt->get($class, $count))
                    Globals::PrimaryPlayerF()->log()->add('Soviele hast du nicht dabei.');
                else
                    Globals::PrimaryPlayerF()->location()->inventory()->add(new $class($count, true));
                break;
            }
    }

    private function inventory_label($id, $text): void
    {
        /** @var Model_Items_Ammobelt $belt */
        $item = Globals::CurrentGameF()->uin()->get($id, 'Interface_Label');
        if (!$item) return;

        /** @var Interface_Label $item */
        $item->set_label($text);
    }

    private function inventory_equip($id, $action): void
    {
        /** @var Model_Items_Abstract_Equipable $item */
        $item = Globals::CurrentGameF()->uin()->get($id, Model_Items_Abstract_Equipable::cls());
        if (!$item || !Globals::PrimaryPlayerF()->inventory()->has($item->uin())) return;

        switch ($action) {
            case 'equip':
                $item->equip(Globals::PrimaryPlayerF());
                if (Tool_System::instance_of($item, 'Interface_Static'))

                    foreach (Globals::PrimaryPlayerF()->inventory()->get(get_class($item)) as $ep)
                        /** @var Model_Items_Abstract_Equipable $ep */
                        if ($ep->uin() !== $item->uin())
                            $ep->equip(Globals::PrimaryPlayerF());
                break;
            case 'unequip':
                $item->unequip();
                if (Tool_System::instance_of($item, 'Interface_Static'))

                    foreach (Globals::PrimaryPlayerF()->inventory()->get(get_class($item)) as $ep)
                        /** @var Model_Items_Abstract_Equipable $ep */
                        if ($ep->uin() !== $item->uin())
                            $ep->unequip();
                break;
            case 'equip_primary':
                if ($item->allows_primary() && $item->is_equipped())
                    $item->equip_primary();
        }
    }

    public function japi_inventory(): void
    {
        //Block sleeping
        if (Globals::PrimaryPlayerF()->get_status()->retrieve('passout') || Globals::PrimaryPlayerF()->get_status()->retrieve('fragile'))
            return;

        //Get params
        $action = self::post('action');
        $items = self::post('items');
        $p = Tool_Scripts::check_comrade(self::post('player'));
        if (!$p && self::post('player'))
            return;

        if (!$p) $p = Globals::PrimaryPlayerF();

        //Check Location
        if (!$p->location()) return;

        //Check params
        if (!in_array($action, ['take','drop','fill','defill','spill','mix','pilldrop','pilltake','belt','label','equip','unequip','equip_primary']))
            return;

        // Remote player
        if ($p->id() !== Globals::PrimaryPlayerF()->id())
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
            if (!Globals::CurrentGameF()->item_available((int)$iid, $p)) {
                unset($items[$i]);
                $lost = true;
            }
        if (!count($items)) return;

        //Transfer items
        if (in_array($action, ['take','drop']))
            $this->inventory_take_drop($action,$items,$p);
        elseif (in_array($action, ['pilldrop','pilltake']))
            $this->inventory_pill($action,$items,(int)self::post('count'));
        elseif ($action === 'fill')
            $this->inventory_fill($items);
        elseif ($action === 'defill')
            $this->inventory_defill($items);
        elseif ($action === 'spill')
            $this->inventory_spill($items, (bool)self::post('all'));
        elseif ($action === 'mix')
            $this->inventory_mix($items);
        elseif ($action === 'belt')
            $this->inventory_belt($items[0], self::post('addr'),(int)self::post('count'));
        elseif ($action === 'label')
            $this->inventory_label($items[0], self::post('text'));
        elseif (in_array($action, ['equip','unequip','equip_primary']))
            $this->inventory_equip($items[0], $action);

        //Message
        if ($lost) Globals::PrimaryPlayerF()->log()->add('Die Aktion konnte nicht vollständig ausgeführt werden, da eines oder mehrere der ausgewählten Gegenstände nicht länger in deiner Reichweite sind.');

        $this->japi_data();
    }

    public function japi_cancel(): bool {
        if (($pid = self::post('p')) && ($p = Globals::CurrentGameF()->get_player($pid))) {
            if (!$p->allow(Interface_Plentity::IC_ALLOW_MANAGE_ACTIVITY)) return $this->japi_data();
        } else $p = Globals::PrimaryPlayerF();

        /** @var Model_Buffs_Abstract_Fragile $buff */
        if (!($buff = $p->get_status()->retrieve('fragile')) || !$buff->abortable())
            return $this->japi_data();
        else {
            $buff->cancel();
            return $this->japi_data();
        }
    }

    public static function code_item($id, $action, $side_id = null, $argument = null, $user = null): void
    {
        //Block sleeping
        if (Globals::PrimaryPlayerF()->get_status()->retrieve('passout') || Globals::PrimaryPlayerF()->get_status()->retrieve('fragile'))
            return;

        $user = $user ? Globals::CurrentGameF()->get_player($user) : null;

        if ($side_id) {
            if ($user && $side_id) return;

            $side_player = Globals::CurrentGameF()->get_player($side_id);

            if (!$side_player || !Tool_Scripts::check_comrade($side_id) || !$side_player->allow(Interface_Plentity::IC_ALLOW_ITEMS_SIDEUSE)) {
                if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayerF()->log()->add('Du kannst aktuell keine Gegenstände auf diesem Spieler anwenden.');
                return;
            }

            $side = Globals::CurrentGameF()->get_player($side_id);
        } else $side = null;

        //Block sleeping (again)
        if ($user && (!$user->allow(Interface_Plentity::IC_ALLOW_ITEMS_USE) || $user->get_status()->retrieve('passout') || $user->get_status()->retrieve('fragile'))) {
            Globals::PrimaryPlayerF()->log()->add('Dieser Spieler kann aktuell keinen Gegenstand verwenden.');
            return;
        }

        //Get Item, or throw Exception if this UIN does not resolve to a valid item
        /** @var Model_Items_Abstract_Item $item */
        $item = Globals::CurrentGameF()->uin()->get((int)$id, Model_Items_Abstract_Item::cls());
        if (!$item || !Globals::CurrentGameF()->item_available((int)$id, $user ?: null)) {
            if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayerF()->log()->add('Die Aktion konnte nicht vollständig ausgeführt werden, da eines oder mehrere der ausgewählten Gegenstände nicht länger in deiner Reichweite sind.');
            return;
        }

        // Prevent the remote use of Virtual Items
        if ($user && Tool_System::instance_of($item, Model_Items_Abstract_Virtual::cls()))
            return;

        if ($user) $user->item_preaction($item,$action);
        if ($r = $item->interact($action, $user ?: Globals::CurrentPlayerF(), $argument, $side))
            Globals::CurrentPlayerF()->location()->log()->add(new Model_Log_Types_Transaction(Model_Log_Types_Transaction::MLTT_USE, $item, $user ? $user->id() : Globals::CurrentPlayerF()->id(), $item->resolve_action($action, $user ?: Globals::CurrentPlayerF())));

        if ($user) {
            $user->item_reaction();
            if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayerF()->log()->add($r ? ':name hat deinen Befehl befolgt und :item eingesetzt!' : ':name konnte :item nicht einsetzen...', [':name' => $user->name()], [':item' => $item->name()]);
        }

    }

    public static function code_npc($id, $action, $side_id = null, $argument = null, $user = null): void
    {
        //Block sleeping
        if (Globals::PrimaryPlayerF()->get_status()->retrieve('passout') || Globals::PrimaryPlayerF()->get_status()->retrieve('fragile'))
            return;

        $user = $user ? Globals::CurrentGameF()->get_player($user) : null;

        if ($side_id) {
            if ($user && $side_id) return;

            $side_player = Globals::CurrentGameF()->get_player($side_id);

            if (!$side_player || !Tool_Scripts::check_comrade($side_id) || !$side_player->allow(Interface_Plentity::IC_ALLOW_ITEMS_SIDEUSE)) {
                Globals::PrimaryPlayerF()->log()->add('Du kannst aktuell keine Gegenstände auf diesem Spieler anwenden.');
                return;
            }

            $side = Globals::CurrentGameF()->get_player($side_id);
        } else $side = null;

        //Block sleeping (again)
        if ($user && (!$user->allow(Interface_Plentity::IC_ALLOW_ITEMS_USE) || $user->get_status()->retrieve('passout') || $user->get_status()->retrieve('fragile'))) {
            Globals::PrimaryPlayerF()->log()->add('Dieser Spieler kann aktuell keinen Gegenstand verwenden.');
            return;
        }

        //Get NPC, or throw Exception if this UIN does not resolve to a valid npc or the npc is at a different location
        $npc = Globals::CurrentGameF()->get_npc($id);
        if (!$npc || ($npc->location_class() !== Globals::PrimaryPlayerF()->location_class())) {
            Globals::PrimaryPlayerF()->log()->add('Die Aktion konnte nicht ausgeführt werden, da der gewählte NPC außerhalb deiner Reichweite ist.');
            return;
        }

        $hid = $npc->hid();
        Globals::PrimaryPlayerF()->get_status()->set_cause_of_death(
            'Vergiftung'
        );
        if ($hid->can($action))
            $hid->perform($action, Globals::PrimaryPlayerF(), $side, $argument);
        Globals::PrimaryPlayerF()->get_status()->clear_cause_of_death();
    }

    public function japi_item(): bool {
        $id = self::post('item');
        if (strpos($id, 'npc//') === 0)
            static::code_npc(substr($id, 5), self::post('action'), self::post('co'), self::post('coarg'), self::post('player'));
        else static::code_item($id, self::post('action'), self::post('co'), self::post('coarg'), self::post('player'));
        return $this->japi_data();
    }
}