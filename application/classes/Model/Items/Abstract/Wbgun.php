<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Wbgun extends Model_Combat_Weapons_Fillable implements Interface_Fillable, Interface_Countable {

	protected static $capacity = 0;
	public static $ammo_icon = 'items/water_variant';

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_WATER;

	public function has_ammo() {
		return ($this->fillrate > 0);
	}
	
	public function consume_ammo() {
		$this->fillrate--;
	}
	
	public function interaction_fillfrom($item) {
		if ($this->fillrate() >= static::$capacity) {
            Globals::PrimaryPlayer()->log()->add(new Model_Log_Types_Text(null, null, 'Der Wasserbehälter dieser Waffe ist leider voll...'));
			return false;
		}	
		
		if (Tool_System::instance_of($item, 'Model_Items_Abstract_Bottle')) {
			/** @var $item Model_Items_Abstract_Bottle */
            if ($item->get_water(1)) $this->fillrate+= 10;
			else Globals::PrimaryPlayer()->log()->add(new Model_Log_Types_Text(null, null, 'Leider ist in dieser Flasche nicht mehr genug Wasser, um diesen Gegenstand zu füllen ...'));

            return true;
		} else return false;
	}
	
	public function interaction_fill($item) {
		if ($this->fillrate() >= static::$capacity) {
            Globals::PrimaryPlayer()->log()->add(new Model_Log_Types_Text(null, null, 'Der Wasserbehälter dieser Waffe ist leider voll...'));
			return false;
		}

		if (Tool_System::instance_of($item, 'Model_Items_Abstract_Liquid')) {
			/** @var $item Model_Items_Abstract_Liquid */
            $this->fillrate += 10;
			$item->consume();
		} else return false;
        return false;
	}
	
	public function capacity() {
		return static::$capacity;
	}
	
	public function fillrate() {
		return ceil($this->fillrate/10);
	}

    public function count() {
        return $this->fillrate;
    }
}	