<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Ghul extends Model_Combat_Zombies_Zombie {

    protected $name;
    protected $max_health = 100;

    protected $stat_initiative = 4;
    protected $stat_damage = 6;
    protected $stat_resistance = 2;
    protected $stat_accuracy = 0;

    protected $movement_range = 5;

    protected $player_id;

    /**
     * @return Model_Combat_Zombies_Ghul
     */
    public static function factory() {
        return parent::factory();
    }

    /**
     * @param null|int $new
     * @return Model_Combat_Zombies_Ghul
     */
    public function zombiefied_player_id($new = null) {
        if ($new === null) return $this->player_id;
        else $this->player_id = $new;
        return $this;
    }

}