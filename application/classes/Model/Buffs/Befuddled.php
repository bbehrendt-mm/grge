<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Befuddled extends Model_Buffs_Abstract_Fragile {

    protected static $name = 'Benebelt';
    protected static $desc = 'Du bist gerade nicht ganz bei dir...';
    protected static $icon = 'befuddled';
    protected static $alt_id = 'befuddled';

    public function __construct($player_id, $lifetime) {
        parent::__construct($player_id, $lifetime);
        new Model_Buffs_Passout($this->assoc_player);
    }

    protected function action_on_complete() {
        if ($this->assoc_player && $buff = $this->assoc_player->get_status()->retrieve('passout')) $buff->unbuff();
    }
}