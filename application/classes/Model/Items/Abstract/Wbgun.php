<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Wbgun extends Model_Combat_Weapons_Fillable implements Interface_Fillable, Interface_Countable {

	protected static $capacity = 0;
	public static $ammo_icon = 'items/water_variant';
	protected static $fillrate_multiplier = 10;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_WATER;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action( 'Füllen' ,
                  Model_Action::factory()
                      ->buttonskin( 'batch' )
                      ->allow_remote(false)
                      ->effect(
                          Model_Effect::factory()
                              ->custom( function(Model_Player $p) {
                                  $bottles = Tool_Scripts::get_items( static::cls() );
                                  $water = Tool_Scripts::get_items( Model_Items_Abstract_Liquid::cls() );

                                  while (!empty($bottles) && !empty($water)) {

                                      /** @var Model_Items_Abstract_Wbgun $latest_bottle */
                                      $latest_bottle = array_pop($bottles);

                                      while ( $latest_bottle->fillrate() < $latest_bottle->capacity() && !empty($water) )
                                          $latest_bottle->interaction_fill( array_pop($water) );
                                  }
                              } )
                      )
            );
    }

	public function has_ammo() {
		return ($this->fillrate > 0);
	}
	
	public function consume_ammo() {
		$this->fillrate--;
	}
	
	public function interaction_fillfrom($item) {
		if ($this->fillrate() >= static::$capacity) {
            Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String( null, 'Der Wasserbehälter dieser Waffe ist leider voll...'));
			return false;
		}	
		
		if (Tool_System::instance_of($item, Model_Items_Abstract_Bottle::cls())) {
			/** @var $item Model_Items_Abstract_Bottle */
            if ($item->get_water(1)) $this->fillrate += static::$fillrate_multiplier;
			else Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String( null, 'Leider ist in dieser Flasche nicht mehr genug Wasser, um diesen Gegenstand zu füllen ...'));

            return true;
		} else return false;
	}
	
	public function interaction_fill($item) {
		if ($this->fillrate() >= static::$capacity) {
            Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String( null, 'Der Wasserbehälter dieser Waffe ist leider voll...'));
			return false;
		}

		if (Tool_System::instance_of($item, Model_Items_Abstract_Liquid::cls())) {
			/** @var $item Model_Items_Abstract_Liquid */
            $this->fillrate += static::$fillrate_multiplier;
			$item->consume();
		} else return false;
        return false;
	}
	
	public function capacity() {
		return static::$capacity;
	}
	
	public function fillrate() {
		return ceil($this->fillrate/static::$fillrate_multiplier);
	}

    public function count() {
        return $this->fillrate;
    }
}	