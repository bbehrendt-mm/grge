<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Clownmask extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Clownsmaske',
			'icon' => 'clownmask',
			'description' => 'Diese Maske ist überraschend stabil, nur leider kannst du nicht sonderlich gut durch sie hindurch sehen...',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 1;
    protected static $destroyed = false;

    // INI, ATK, DEF, ACC
    protected static $effects = [-1,2,8,-2];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_HELMET;
    protected static $protection = 50;
}	