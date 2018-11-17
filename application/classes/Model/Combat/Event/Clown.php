<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Event_Clown extends Model_Combat_Players_Player {

    protected static $default_max_health = 100;
    protected static $default_stat_initiative = 2;
    protected static $default_stat_damage = 20;
    protected static $default_stat_resistance = 0;
    protected static $default_stat_accuracy = 20;
    protected static $movement_range = 2;

    protected static $show_weapon_switch = false;

    /**
     * @param Interface_Plentity $p
     *
     * @return Model_Combat_Players_Dog
     * @throws Exception
     */
    public static function create_linked_actor($p) {
        /** @noinspection PhpUndefinedMethodInspection */
        $ret = static::factory()
            ->player($p)
            ->name($p->name(), Model_Combat_Actor::MCA_TYPE_PLAYER)
            ->strength($p->get_status()->get(Model_Status::MS_STAT_HEALTH), 100, 1)
            ->add_weapon(new Model_Items_Knife());

        return $ret;
    }

    protected function damage($damage, $from = null, $armor_damage = null): void {
        parent::damage($damage, $from, $armor_damage);

        $this->player->get_status()->modify(Model_Status::MS_STAT_HEALTH, -$damage, Model_Status::MS_EFFECT_UNSCALE);
    }

    public function customSprite($death_sprite = false): ?string {
        return $death_sprite ? 'player_dead.gif' : 'zombie_clown.gif';
    }

    public function get_avatar(): ?string {
        return 'media/icons/battle/avatar/clown.jpg';
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