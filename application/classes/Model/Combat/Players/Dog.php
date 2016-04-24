<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Players_Dog extends Model_Combat_Players_Player {

    protected $max_health = 50;

    protected $stat_initiative = 10;
    protected $stat_damage = 3;
    protected $stat_resistance = 0;
    protected $stat_accuracy = 5;
    protected $movement_range = 8;
    protected $avatar;

    protected static $show_weapon_switch = false;

    /**
     * @param Interface_Plentity $p
     * @param string $avatar
     * @return Model_Combat_Players_Dog
     */
    public static function create_linked_actor($p, $avatar = 'dog.jpg') {
        /** @noinspection PhpUndefinedMethodInspection */
        $ret = static::factory()
            ->player($p)
            ->name($p->name(), Model_Combat_Actor::MCA_TYPE_PLAYER)
            ->strength($p->get_status()->get(Model_Status::MS_STAT_HEALTH)/2, 50, 1)
            ->add_weapon(new Model_Items_Dogbite());

        /** @var $ret Model_Combat_Players_Dog */
        $ret->avatar = $avatar;

        return $ret;
    }

    protected function damage($damage, $from = null, $armor_damage = null) {
        parent::damage($damage, $from, $armor_damage);

        $this->player->get_status()->modify(Model_Status::MS_STAT_HEALTH, -$damage * 2, Model_Status::MS_EFFECT_UNSCALE);
    }

    public function customSprite($death_sprite = false) {
        return $death_sprite ? 'pet_dead.gif' : 'dog.gif';
    }

    /**
     * @param $damage
     * @param $kills
     * @param $death
     * @param $target
     */
    protected function score_kills($damage, $kills, $death, $target) {}

    public function get_avatar() {
        return 'media/icons/battle/avatar/' . $this->avatar;
    }

    /**
     * @param Model_Combat_Weapon|Model_Combat_Weapon[] $weapon
     * @return Model_Combat_Actor
     */
    public function add_weapon($weapon) {
        if (!is_array($weapon))
            $weapon->ignore_equip();

        return parent::add_weapon($weapon);
    }
}