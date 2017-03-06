<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Drunk extends Model_Buffs_Abstract_Fragile {

    protected static $name = 'Schlafen';
    protected static $desc = 'Du hast es mit deinem Alkoholkonsum etwas übertrieben und bist eingeschlafen.';
    protected static $icon = 'sleep_drunk';

    protected static $abortable = false;

    public function __construct($player_id) {
        parent::__construct($player_id, 18);
    }

    protected $effects = Array(
        Model_Status::MS_STAT_ENERGY => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0.6,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
        Model_Status::MS_STAT_DRUNK => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 1,
        ),
        Model_Status::MS_STAT_SLEEPY => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 1.1,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
    );

    protected function action_on_complete() {
        if ($this->associated_to_player() && Tool_Scripts::location_type($this->assoc_player->location_class()) == 2) {
            //ToDO: Fix for use with multiple rooms
            $l = $this->assoc_player->location();
            if		($l->room()->has_content('bedr3'))	new Model_Buffs_Sleep($this->assoc_player, 3);
            elseif	($l->room()->has_content('bedr2'))	new Model_Buffs_Sleep($this->assoc_player, 2);
            elseif	($l->room()->has_content('bedr1'))	new Model_Buffs_Sleep($this->assoc_player, 1);
            else								new Model_Buffs_Sleep($this->assoc_player, 0);
        }

    }
}