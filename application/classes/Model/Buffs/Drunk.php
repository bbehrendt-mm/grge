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
        Model_Player::MP_STAT_ENERGY => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0.6,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
        Model_Player::MP_STAT_DRUNK => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 1,
        ),
        Model_Player::MP_STAT_SLEEPY => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 1.1,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
    );

    protected function action_on_complete() {
        if (Tool_Scripts::location_type($this->assoc_player->location_class()) == 2) {

            /** @var Model_Places_Abstract_Hideout $l */
            $l = $this->assoc_player->location();
            if		($l->home_extensions("bed", "lv3"))	new Model_Buffs_Sleep($this->assoc_player->id(), 3);
            elseif	($l->home_extensions("bed", "lv2"))	new Model_Buffs_Sleep($this->assoc_player->id(), 2);
            elseif	($l->home_extensions("bed", "lv1"))	new Model_Buffs_Sleep($this->assoc_player->id(), 1);
            else										new Model_Buffs_Sleep($this->assoc_player->id(), 0);
        }

    }
}