<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Hulk extends Model_Buffs_Abstract_Buff {

    protected static $name = 'Hulkout';
    protected static $icon = 'hulk';
    protected static $bid = 'hulk';
    protected static $desc = 'UAAAAAAARGH! HOCH DAMIT!';

    protected function apply() {
        parent::apply();
        $this->assoc_player->inventory()->limit($this->assoc_player->inventory()->limit() + 70);
    }

    public function unbuff() {
        parent::unbuff();
        $this->assoc_player->inventory()->limit($this->assoc_player->inventory()->limit() - 70);
    }
}
