<?php defined('SYSPATH') or die('No direct script access.');

interface Interface_Tickable {

    public function tick($id, $player_tick = true);

}