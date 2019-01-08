<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Zombies_Zombie extends Model_Combat_Actor {

    protected static $default_weapon = 'Model_Items_Claw';
    protected $nano_npc;

    protected static $taunts = [
        'drunk' => ['*hicks*'],
        'begin' => ['... GEHIIIIRN ...','... RAAAAH!','...','*gurgle*','... WÄHLT ... AFD ...']
    ];

    protected static $is_unique = false;

    protected static $num_str = 1;

    public function __construct() {
        parent::__construct();
        $this->type = Model_Combat_Actor::MCA_TYPE_ZOMBIE;
        $this->nano_npc = new Model_NPC_Nano($this->name());
        $this->nano_npc->get_status()->set(Model_Status::MS_STAT_ENERGY, 50);
        $this->nano_npc->location_class(
            Globals::hasCurrentLocation() ?
                Globals::getCurrentLocationF()->uin() :
                -1
        );
    }

    public static function get_strength_quantifier(): int
    {
        return static::$num_str;
    }

    /**
     * @param Model_Combat_Weapon|Model_Combat_Weapon[] $weapon
     *
     * @return Model_Combat_Actor
     * @throws Exception
     */
    public function add_weapon($weapon): Model_Combat_Actor {
        if (!is_array($weapon)) {
            $weapon->register($this->nano_npc);
            $weapon->ignore_equip();
        }

        return parent::add_weapon($weapon);
    }

    public static function factory(): Model_Combat_Actor {
        $tmp = parent::factory();
        if (static::$default_weapon)
            $tmp->add_weapon(new static::$default_weapon);
        return $tmp;
    }

    public function get_avatar(): ?string {
        return 'media/icons/battle/avatar/zombie.jpg';
    }
}