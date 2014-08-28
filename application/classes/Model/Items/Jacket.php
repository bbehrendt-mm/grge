<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Jacket extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Lederjacke',
			'icon' => 'jacket',
			'description' => 'In der Postapokalypse musst du als Überlebender, der etwas auf sich hält, selbstverständlich eine Lederjacke tragen. Wenn du den Mad-Max-Look vollständig umsetzen willst musst du dir allerdings noch irgendwo eine Portion Antisemitismus besorgen...',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 3;

    protected static $atype = Model_Items_Abstract_Armor::MIAA_BODY;
    protected $protection = 70;
    protected static $damage_reduction = 0;
    protected static $damage_blocking = 1.5;
    protected static $destroyed = null;
}	