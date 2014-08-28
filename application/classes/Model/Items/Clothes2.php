<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Clothes2 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Verstärkte Straßenkleidung',
			'icon' => 'clothes3',
			'description' => 'Das ist der letzte Fashion-Schrei aus der neuen Mad Max Collection - und praktisch ist es auch noch. Durch die verschiedenen Schutzschichten dieser verstärkten Straßenkleidung beisst sich so schnell kein Zombie durch!',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 5;

    protected static $atype = Model_Items_Abstract_Armor::MIAA_BODY;
    protected $protection = 50;
    protected static $damage_reduction = 0;
    protected static $damage_blocking = 2;
    protected static $destroyed = 'Model_Items_Generic_Clothes';
}	