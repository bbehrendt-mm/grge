<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Halloween_Helmet extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Kürbishelm',
			'icon' => 'hw19_helmet1',
			'description' => 'Wie nur wenige Leute wissen, eignet sich ein Kürbis großartig als Helm - insbesondere, wenn man keinen besonderen Schutz von ihm erwartet.',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 1;

    protected static $temperature_isolation_abs = 1.0;

    // INI, ATK, DEF, ACC
    protected static $effects = [1,0,2,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_HELMET;
    protected static $protection = 20;
}	