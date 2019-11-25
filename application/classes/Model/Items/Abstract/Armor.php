<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Armor extends Model_Items_Abstract_Equipable {

    protected static $protection = 1;
    protected $current_protection;

    protected static $temperature_isolation_abs = 0.0;
    protected static $temperature_isolation_rel = 0.0;

    protected static $destroyed;

    public function getAbsoluteTemperatureIsoloation(): float {
        return static::$temperature_isolation_abs;
    }

    public function getRelativeTemperatureIsoloation(): float {
        return static::$temperature_isolation_rel;
    }

    public function convertStringProtection(): string {
        if (static::$protection > 100) return 'Sehr stabil';
        elseif (static::$protection > 75) return 'Stabil';
        elseif (static::$protection > 50) return 'Durchschnittlich';
        elseif (static::$protection > 25) return 'Wackelig';
        elseif (static::$protection > 10) return 'Instabil';
        elseif (static::$protection > 5) return 'Sehr Instabil';
        else return 'Desolat';
    }

    /**
     * Item constructor
     * Will randomly select a subtype if subtypes are defined for this item class
     *
     * @param null $type
     *
     * @throws Exception
     */
    public function __construct($type = null) {
        parent::__construct($type);
        $this->current_protection = static::$protection;
    }

    public function is_destroyed(): bool
    {
        return ($this->get_protection() <= 0);
    }

    public function get_destroyed_class() {
        return static::$destroyed;
    }

    public function get_protection() {
        return min(static::$protection, $this->current_protection);
    }

    public function get_hp() {
        return min(1, max(0, $this->current_protection/static::$protection));
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
     *
     * @throws Exception
     */
    public function take_damage($damage): void
    {
        $this->current_protection -= $damage;
        if ($this->get_protection() <= 0) {
            if ($this->player_id && $this->get_destroyed_class() && ($p = Globals::CurrentGameF()->get_player($this->player_id))) {
                $tmp = $this->get_destroyed_class();
                $p->location()->inventory()->add(new $tmp);
            }
            $this->consume();
        }

    }
}	