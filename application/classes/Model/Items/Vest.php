<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Vest extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Schutzweste',
			'icon' => 'vest',
			'description' => 'Diese aus Polizeibeständen stammende Weste schützt dich zuverlässig vor Schlägen, Tritten, Schüssen und Aliens (allerdings nur, wenn die keine Laserwaffen haben).',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 4;

    // INI, ATK, DEF, ACC
    protected static $effects = [0,0,4,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_BODY;
    protected $protection = 100;
    protected static $destroyed = null;
}	