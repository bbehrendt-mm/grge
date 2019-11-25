<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Halloween_Jacket3 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Dämonische Kürbisjacke',
			'icon' => 'hw19_armor3',
			'description' => 'Warum sollte man komplizierte satanische Rituale über sich ergehen lassen, wenn man den selben Effekt auch mit einer stylischen und leicht verrottet riechenden Jacke erreichen kann?',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 2;

    protected static $temperature_isolation_abs = 10.0;

    // INI, ATK, DEF, ACC
    protected static $effects = [-2,2,8,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_BODY;
    protected static $protection = 55;
}	