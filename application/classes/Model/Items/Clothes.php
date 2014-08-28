<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Clothes extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Straßenkleidung',
			'icon' => 'clothes',
			'description' => 'Die Zombies haben dir alles genommen - bis auf die Kleidung, die du trägst. Da du sie aber schon einige Wochen ununterbrochen trägst, kann man verstehen, dass die Zombies mit diesen stinkenden Lumpen nichts zu tun haben wollen.',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 0;

    protected static $atype = Model_Items_Abstract_Armor::MIAA_BODY;
    protected $protection = 10;
    protected static $damage_reduction = 0;
    protected static $damage_blocking = 1;
    protected static $destroyed = 'Model_Items_Generic_Clothes';
}	