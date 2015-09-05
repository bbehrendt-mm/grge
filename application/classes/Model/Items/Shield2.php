<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Shield2 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Solider Holzkistendeckel',
			'icon' => 'shield2',
			'description' => 'Dieser Holzkistendeckel sieht relativ stabil aus... du könntest damit sicherlich Zombies abwehren, aber übertreib es besser nicht.',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 17;

    // INI, ATK, DEF, ACC
    protected static $effects = [-5,0,8,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_SHIELD;
    protected $protection = 45;
    protected static $destroyed = null;
}	