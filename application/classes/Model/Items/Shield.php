<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Shield extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Holzkistendeckel',
			'icon' => 'shield',
			'description' => 'Dieses zusammengenagelte und verrottete Stück Holz sieht nicht allzu stabil aus... Du könntest es benutzen, um dich vor Zombies zu verteidigen. Allerdings solltest du nicht überrascht sein, wenn dir das Teil in der Hand zerbröselt.',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 10;

    protected static $atype = Model_Items_Abstract_Armor::MIAA_SHIELD;
    protected static $acover = Model_Items_Abstract_Armor::MIAA_FULL;
    protected $protection = 5;
    protected static $damage_reduction = 1;
    protected static $damage_blocking = 0;
    protected static $destroyed = null;
}	