<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Armor extends Model_Items_Abstract_Equipable {

    protected $protection = 1;
    protected $current_protection;

    protected static $destroyed = null;

    public function convertStringProtection() {
        if ($this->protection > 100) return "Sehr stabil";
        elseif ($this->protection > 75) return "Stabil";
        elseif ($this->protection > 50) return "Durchschnittlich";
        elseif ($this->protection > 25) return "Wackelig";
        elseif ($this->protection > 10) return "Instabil";
        elseif ($this->protection > 5) return "Sehr Instabil";
        else return "Desolat";
    }

    /**
     * Item constructor
     * Will randomly select a subtype if subtypes are defined for this item class
     */
    public function __construct($type = null) {
        parent::__construct($type);
        $this->current_protection = $this->protection;
    }

    public function is_destroyed() {
        return ($this->get_protection() <= 0);
    }

    public function get_destroyed_class() {
        return static::$destroyed;
    }

    public function get_protection() {
        return min($this->protection, $this->current_protection);
    }

    public function get_hp() {
        return min(1, max(0, $this->current_protection/$this->protection));
    }

    public function drop_dead() {
        if (static::$destroyed === false)
            return parent::drop_dead();
        if (static::$destroyed)
            return new static::$destroyed;
        else return null;
    }

    /**
     * @param number $damage
     */
    public function take_damage($damage) {
        $this->current_protection -= $damage;
        if ($this->get_protection() <= 0) {
            if ($this->get_destroyed_class() && $this->player_id && ($p = Globals::CurrentGame()->get_player($this->player_id))) {
                $tmp = $this->get_destroyed_class();
                $p->location()->inventory()->add(new $tmp);
            }
            $this->consume();
        }

    }
}	