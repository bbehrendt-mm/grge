<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Escape extends Model_Items_Abstract_Item {
	
	protected static $esc_value = 0;
	
	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_FIGHT;
	
	public function escape(): int {
		return static::$esc_value;
	}
	
}	