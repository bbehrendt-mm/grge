<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Abstract_Node extends Model_Places_Abstract_Place {
    protected static $icon = 'desert';

    public function find_item($force = false) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        if (!$player->buff_retr('fragile') && !$player->buff_retr('passout') && Tool_Events::current($game->next_tick()) == 'halloween') {
            if (mt_rand(0,100) > 90)
                Tool_Scripts::place_new_item(new Model_Items_Generic_Pumpkin());
        }

        return parent::find_item($force);
    }
}	