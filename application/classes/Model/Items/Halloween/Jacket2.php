<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Halloween_Jacket2 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Verdorbene Kürbisjacke',
			'icon' => 'hw19_armor2',
			'description' => 'Es gibt sicherlich nichts, was dagegen spricht, eine mit Geisterenergie aufgeladene Jacke aus Kürbisfetzen zu tragen ...',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 2;

    protected static $temperature_isolation_abs = 5.0;

    // INI, ATK, DEF, ACC
    protected static $effects = [-1,1,6,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_BODY;
    protected static $protection = 45;
}	