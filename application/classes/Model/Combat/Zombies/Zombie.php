<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Zombies_Zombie extends Model_Combat_Actor {

    protected $type = Model_Combat_Actor::MCA_TYPE_ZOMBIE;

    public static function factory() {
        return parent::factory()
            ->add_weapon(new Model_Items_Claw());
    }

    public function get_avatar() {
        return 'media/icons/battle/avatar/zombie.jpg';
    }
}