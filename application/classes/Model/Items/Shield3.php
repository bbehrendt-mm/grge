<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Shield3 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Autotür',
			'icon' => 'door',
			'description' => 'Autotüren geben großartige Schilde ab! Sie haben einen Griff zum Halten, sind sehr stabil, groß und nehmen dir durch das Fenster noch nicht einmal die Sicht. Leider sind sie auch ziemlich schwer...',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 65;

    // INI, ATK, DEF, ACC
    protected static $effects = [-10,0,12,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_SHIELD;

    protected static $protection = 100;
}	