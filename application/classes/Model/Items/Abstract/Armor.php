<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Armor extends Model_Items_Abstract_Equipable {

    protected $protection = 1;
    protected static $destroyed = null;

    public static function convertStringType() {
        switch (static::$equipment_type) {
            case static::MIAE_ARMOR_BODY: return 'Rüstung';
            case static::MIAE_ARMOR_HELMET: return 'Helm';
            case static::MIAE_ARMOR_SHIELD: return 'Schild';
            case static::MIAE_ARMOR_CAPE: return 'Umhang';
            default: return 'Unbekannt';
        }
    }

    public function convertStringProtection() {
        if ($this->protection > 100) return "Sehr stabil";
        elseif ($this->protection > 75) return "Stabil";
        elseif ($this->protection > 50) return "Durchschnittlich";
        elseif ($this->protection > 25) return "Wackelig";
        elseif ($this->protection > 10) return "Instabil";
        elseif ($this->protection > 5) return "Sehr Instabil";
        else return "Desolat";
    }

    public function is_destroyed() {
        return ($this->protection <= 0);
    }

    public function get_destroyed_class() {
        return static::$destroyed;
    }

    public function get_protection() {
        return $this->protection;
    }

    public function drop_dead() {
        if (static::$destroyed)
            return new static::$destroyed;
        else return null;
    }

    /**
     * @param number $damage
     */
    public function take_damage($damage) {
        $this->protection -= $damage;
        if ($this->protection <= 0)
            $this->unequip();
    }
}	