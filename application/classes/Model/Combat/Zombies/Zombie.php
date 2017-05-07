<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Zombies_Zombie extends Model_Combat_Actor {

    protected static $default_weapon = 'Model_Items_Claw';
    protected $type = Model_Combat_Actor::MCA_TYPE_ZOMBIE;
    protected $nano_npc;

    protected static $is_unique = false;

    protected static $num_str = 1;

    public function __construct() {
        parent::__construct();
        $this->nano_npc = new Model_NPC_Nano($this->name());
        $this->nano_npc->get_status()->set(Model_Status::MS_STAT_ENERGY, 50);
        $this->nano_npc->location_class(Globals::CurrentPlayer() ? Globals::CurrentPlayer()->location_class() : -1);
    }

    public static function get_strength_quantifier() {
        return static::$num_str;
    }

    /**
     * @param Model_Combat_Weapon|Model_Combat_Weapon[] $weapon
     * @return Model_Combat_Actor
     */
    public function add_weapon($weapon) {
        if (!is_array($weapon)) {
            $weapon->register($this->nano_npc);
            $weapon->ignore_equip();
        }

        return parent::add_weapon($weapon);
    }

    public static function factory() {
        $tmp = parent::factory();
        if (static::$default_weapon)
            $tmp->add_weapon(new static::$default_weapon);
        return $tmp;
    }

    public function get_avatar() {
        return 'media/icons/battle/avatar/zombie.jpg';
    }
}