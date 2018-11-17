<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Players_Cat extends Model_Combat_Players_Player {

    protected static $default_max_health = 25;

    protected static $default_stat_initiative = 10;
    protected static $default_stat_damage = 4;
    protected static $default_stat_resistance = 1;
    protected static $default_stat_accuracy = 10;
    protected static $movement_range = 10;

    protected static $show_weapon_switch = false;
    protected $avatar;

    protected static $taunts = [
        'drunk' => ['Miiiiii.... *hicks*'],
        'begin' => ['MIAU!','*zisch*']
    ];

    /**
     * @param Interface_Plentity $p
     * @param string             $avatar
     *
     * @return Model_Combat_Players_Dog
     * @throws Exception
     */
    public static function create_linked_actor($p, $avatar = 'cat.jpg') {
        /** @noinspection PhpUndefinedMethodInspection */
        $ret = static::factory()
            ->player($p)
            ->name($p->name(), Model_Combat_Actor::MCA_TYPE_PLAYER)
            ->strength($p->get_status()->get(Model_Status::MS_STAT_HEALTH)/4, 25, 1)
            ->add_weapon(new Model_Items_Catclaw());

        /** @var $ret Model_Combat_Players_Cat */
        $ret->avatar = $avatar;

        if ($p->get_status()->get(Model_Status::MS_STAT_DRUNK) > 25) $ret->add_modifier("drunk", ($p->get_status()->get(Model_Status::MS_STAT_DRUNK)-25)*(4/300));

        return $ret;
    }

    protected function damage($damage, $from = null, $armor_damage = null): void {
        parent::damage($damage, $from, $armor_damage);

        $this->player->get_status()->modify(Model_Status::MS_STAT_HEALTH, -$damage * 4, Model_Status::MS_EFFECT_UNSCALE);
    }

    public function customSprite($death_sprite = false): ?string {
        return $death_sprite ? 'pet_dead.gif' : 'cat.gif';
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