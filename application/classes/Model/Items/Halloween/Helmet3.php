<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Halloween_Helmet3 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Dämonischer Kürbishelm',
			'icon' => 'hw19_helmet1',
			'description' => 'Dieser Kürbishelm trägt das Qualitätssiegel Silver Shamrock und wird daher deine Seele definitiv NICHT in die ewige Verdammnis stürzen.',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 1;

    // INI, ATK, DEF, ACC
    protected static $effects = [2,2,4,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_HELMET;
    protected static $protection = 60;
}	