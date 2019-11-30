<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Players_Rudolph extends Model_Combat_Players_Player {

    protected static $default_max_health = 200;

    protected static $default_stat_initiative = 10;
    protected static $default_stat_damage = 10;
    protected static $default_stat_resistance = 8;
    protected static $default_stat_accuracy = 10;

    protected static $movement_range = 33;
    protected $avatar;

    protected static $taunts = [
        'drunk' =>   ['... hihihiiii *hicks*', 'Aussm ... Weeeeeeg! *hicks*', '*hicks* Lalalaaaaaa' ],
        'berserk' => ['WEIHNACHTEN, IHR MOTHERFUCKER!!!'],
        'begin' =>   ['Euch mach ich fertig!', 'Jetzt gibt\'s aufs Maul!']
    ];

    protected static $show_weapon_switch = false;

    /**
     * @param Interface_Plentity $p
     * @param string $avatar
     *
     * @param bool $laser
     * @param bool $passive_laser
     * @param bool $reload_laser
     * @return Model_Combat_Players_Rudolph
     * @throws Exception
     */
    public static function create_linked_actor($p, $avatar = 'dog.jpg', bool $laser = false, bool $passive_laser = false, bool $reload_laser = false): Model_Combat_Players_Player
    {
        /** @var Model_Combat_Players_Rudolph $ret */
        $ret = static::factory();
        $ret
            ->player($p)
            ->name($p->name(), Model_Combat_Actor::MCA_TYPE_PLAYER)
            ->strength($p->get_status()->get(Model_Status::MS_STAT_HEALTH) * 2, 200, 1);

        if ($laser && ($p->get_status()->get(Model_Status::MS_STAT_DRUNK) > 50 || $passive_laser))
            $ret->add_weapon(new Model_Items_Noselaser(null, $reload_laser));

        $ret->add_weapon(new Model_Items_Hoof());
        $ret->avatar = $avatar;

        $ret->transfer_stats($p);

        return $ret;
    }

    protected function damage($damage, $from = null, $armor_damage = null): void {
        parent::damage($damage, $from, $armor_damage);

        $this->player->get_status()->modify(Model_Status::MS_STAT_HEALTH, -$damage / 2.0, Model_Status::MS_EFFECT_UNSCALE);
    }

    public function customSprite($death_sprite = false): ?string {
        return $death_sprite ? 'pet_dead.gif' : ($this->ki_mod_is_registered('drunk') ? 'reindeer_on.gif' : 'reindeer.gif');
    }

    public function get_avatar(): ?string {
        return 'media/icons/battle/avatar/' . $this->avatar;
    }

    /**
     * @param Model_Combat_Weapon|Model_Combat_Weapon[] $weapon
     *
     * @return Model_Combat_Actor
     * @throws Exception
     */
    public function add_weapon($weapon): Model_Combat_Actor {
        if (!is_array($weapon))
            $weapon->ignore_equip();

        return parent::add_weapon($weapon);
    }
}