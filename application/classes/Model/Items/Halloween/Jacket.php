<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Halloween_Jacket extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Kürbisjacke',
			'icon' => 'hw19_armor1',
			'description' => 'Diese hochgradig umweltfreundliche Rüstung bietet nicht nur minimalen Schutz vor Zombies, sie ist außerdem noch biologisch abbaubar!',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 2;

    protected static $temperature_isolation_abs = 5.0;

    // INI, ATK, DEF, ACC
    protected static $effects = [-1,0,4,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_BODY;
    protected static $protection = 25;
}	