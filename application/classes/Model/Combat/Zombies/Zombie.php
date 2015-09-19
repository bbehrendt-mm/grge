<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Zombies_Zombie extends Model_Combat_Actor {

    protected $type = Model_Combat_Actor::MCA_TYPE_ZOMBIE;
    protected $pseudoplayer;

    public function __construct() {
        /** @global Model_Player $player */
        global $player;
        parent::__construct();
        $this->pseudoplayer = new Model_Pseudoplayer([Model_Player::MP_STAT_ENERGY => 50], $player->location_class());
    }

    /**
     * @param Model_Combat_Weapon|Model_Combat_Weapon[] $weapon
     * @return Model_Combat_Actor
     */
    public function add_weapon($weapon) {
        if (!is_array($weapon)) {
            $weapon->register($this->pseudoplayer);
            $weapon->ignore_equip();
        }


        return parent::add_weapon($weapon);
    }

    public static function factory() {
        return parent::factory()
            ->add_weapon(new Model_Items_Claw());
    }

    public function get_avatar() {
        return 'media/icons/battle/avatar/zombie.jpg';
    }
}