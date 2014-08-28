<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Shield3 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Autotür',
			'icon' => 'door',
			'description' => 'Autotüren geben großartige Schilde ab! Sie haben einen Griff zum Halten, sind sehr stabil, groß und nehmen dir durch das Fenster noch nicht einmal die Sicht. Leider sind sie auch ziemlich schwer...',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 65;

    protected static $atype = Model_Items_Abstract_Armor::MIAA_SHIELD;
    protected static $acover = Model_Items_Abstract_Armor::MIAA_FULL;
    protected $protection = 100;
    protected static $damage_reduction = 2;
    protected static $damage_blocking = 0.75;
    protected static $destroyed = null;
}	