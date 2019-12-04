<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Heatjacket3 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Winterjacke',
			'icon' => 'heatsuit3',
			'description' => 'Diese Winterjacke schützt dich recht zuverlässig vor Unterkühlung und spontanem Gefrierbrand.',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 0;

    protected static $temperature_isolation_abs = 10;

    // INI, ATK, DEF, ACC
    protected static $effects = [0,0,0,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_BODY;
    protected static $protection = 15;
    protected static $damage_reduction = 0;
    protected static $damage_blocking = 2;
    protected static $destroyed = 'Model_Items_Generic_Clothes';
}	