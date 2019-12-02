<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Heatjacket2 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Santa-Mütze',
			'icon' => 'heatsuit2',
			'description' => 'Leider gibt es keinen weißen Bart hierzu - aber zumindest hält es warm!',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 0;

    protected static $temperature_isolation_abs = 3.5;

    // INI, ATK, DEF, ACC
    protected static $effects = [0,0,0,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_HELMET;
    protected static $protection = 5;
    protected static $damage_reduction = 0;
    protected static $damage_blocking = 1;
    protected static $destroyed = 'Model_Items_Generic_Cloth';
}	