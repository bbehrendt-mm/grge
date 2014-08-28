<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Shield2 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Solider Holzkistendeckel',
			'icon' => 'shield2',
			'description' => 'Dieser Holzkistendeckel sieht relativ stabil aus... du könntest damit sicherlich Zombies abwehren, aber übertreib es besser nicht.',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 17;

    protected static $atype = Model_Items_Abstract_Armor::MIAA_SHIELD;
    protected static $acover = Model_Items_Abstract_Armor::MIAA_FULL;
    protected $protection = 45;
    protected static $damage_reduction = 1;
    protected static $damage_blocking = 0;
    protected static $destroyed = null;
}	