<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Players_Player extends Model_Combat_Actor {

    /** @var Model_Player */
    protected $player;


    /**
     * @param null|Model_Player $p
     * @return Model_Combat_Players_Player|Model_Player
     */
    public function player($p = null) {
        if ($p === null) return $this->player;
        else $this->player = $p;
        return $this;
    }

    /**
     * @param Model_Player $p
     * @return Model_Combat_Players_Player
     */
    public static function create_linked_actor($p) {

        /** @noinspection PhpUndefinedMethodInspection */
        $ret = static::factory()
            ->player($p)
            ->name($p->name(), Model_Combat_Actor::MCA_TYPE_PLAYER)
            ->strength($p->stats_get(Model_Player::MP_STAT_HEALTH), 100, 1)
            ->register_inventory($p->inventory())
            ->add_weapon(new Model_Items_Fist());

        //TODO Implement stats

        return $ret;
    }

    protected function damage($damage, $from = null) {
        parent::damage($damage, $from);

        $this->player->stats_modify([Model_Player::MP_STAT_HEALTH, -$damage]);
    }

    /**
     * @param Model_Combat_Weapon|Model_Combat_Weapon[] $weapon
     * @return Model_Combat_Actor
     */
    public function add_weapon($weapon) {
        if (!is_array($weapon))
            $weapon->register($this->player);

        return parent::add_weapon($weapon);
    }

    public function disengage() {
        foreach ($this->weapons as $weapon)
            $weapon->unregister();
    }

    /**
     * @param $damage
     * @param $kills
     * @param $death
     * @param $target
     */
    protected function score_kills($damage, $kills, $death, $target) {
        if ($kills > 0 && $target->get_type() == static::MCA_TYPE_ZOMBIE)
            $this->player->achievements()->achieve(Model_Achievement::MA_KILLED_ZOMBIES, $kills);

        if ($kills > 0 && Tool_System::instance_of($target, 'Model_Combat_Zombies_Ghul'))
            $this->player->achievements()->achieve(Model_Achievement::MA_MERCYKILL, $kills);
    }
}