<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Clothes2 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Verstärkte Straßenkleidung',
			'icon' => 'clothes3',
			'description' => 'Das ist der letzte Fashion-Schrei aus der neuen Mad Max Collection - und praktisch ist es auch noch. Durch die verschiedenen Schutzschichten dieser verstärkten Straßenkleidung beisst sich so schnell kein Zombie durch!',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 5;

    // INI, ATK, DEF, ACC
    protected static $effects = [0,1,3,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_BODY;
    protected $protection = 50;
    protected static $destroyed = 'Model_Items_Generic_Clothes';
}	