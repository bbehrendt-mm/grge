<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Ghul extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'ghul.gif';

    protected static $default_max_health = 100;
    protected static $default_stat_initiative = 4;
    protected static $default_stat_damage = 6;
    protected static $default_stat_resistance = 2;
    protected static $default_stat_accuracy = 0;
    //protected static $movement_range = 5;

    protected static $num_str = 100;

    protected $player_id;

    protected static $is_unique = true;

    /**
     * @return Model_Combat_Zombies_Ghul
     */
    public static function factory(): Model_Combat_Actor {
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