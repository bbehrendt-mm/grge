<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Act extends Controller_Game {

    public function japi_inventory() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Battle_Weapon
         */
        global $game, $player;

        //Block sleeping
        if ($player->buff_retr('passout') || $player->buff_retr('fragile'))
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
        if (!in_array($action, ($p->id() != $player->id()) ? ['take'] : ['take','drop']))
            return;

        $lost = false;
        foreach ($items as $i => $iid)
            if (!$game->item_available((int)$iid)) {
                unset($items[$i]);
                $lost = true;
            }

        //Actually transfer items
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
                } else {
                    if (!$item->take(count($items) > 1)) continue;
                }

                if (!$p->location()->inventory()->remove($itemid)) continue;
                if (!$p->inventory()->add($item)) $p->location()->inventory()->add($item);
            }

        //Message
        if ($lost) $player->log()->add('Die Aktion konnte nicht vollständig ausgeführt werden, da eines oder mehrere der ausgewählten Gegenstände nicht länger in deiner Reichweite sind.');

        $this->japi_data();
    }
    public function japi_item() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $game, $player;

        //Block sleeping
        if ($player->buff_retr('passout') || $player->buff_retr('fragile'))
            return $this->japi_data();

        //Get UIN
        $id = (int)$this->request->post('item');
        $action = $this->request->post('action');
        if ($coid = $this->request->post('coitem')) {
            $argument = $game->uin()->get($coid, 'Model_Items_Abstract_Item');
            if (!$game->item_available($coid) || !$argument) {
                $player->log()->add('Die Aktion konnte nicht vollständig ausgeführt werden, da eines oder mehrere der ausgewählten Gegenstände nicht länger in deiner Reichweite sind.');
                return $this->japi_data();
            }
        } else $argument = $this->request->post('coarg');

        if ($side_id = $this->request->post('side')) {
            if (!Tool_Scripts::check_comrade($side_id)) {
                $player->log()->add('Dieser Spieler befindet sich nicht in deiner Nähe.');
                return $this->japi_data();
            }

            $side = $game->get_player($side_id);
        } else $side = null;

        //Get Item, or thow Exception if this UIN does not resolve to a valid item
        $item = $game->uin()->get($id, 'Model_Items_Abstract_Item');
        if (!$game->item_available($id) || !$item) {
            $player->log()->add('Die Aktion konnte nicht vollständig ausgeführt werden, da eines oder mehrere der ausgewählten Gegenstände nicht länger in deiner Reichweite sind.');
            return $this->japi_data();
        }

        $item->interact($action, $argument, $side);

        return $this->japi_data();
    }

}