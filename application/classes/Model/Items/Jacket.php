<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Jacket extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Lederjacke',
			'icon' => 'jacket',
			'description' => 'In der Postapokalypse musst du als Überlebender, der etwas auf sich hält, selbstverständlich eine Lederjacke tragen. Wenn du den Mad-Max-Look vollständig umsetzen willst musst du dir allerdings noch irgendwo eine Portion Antisemitismus besorgen...',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 3;

    // INI, ATK, DEF, ACC
    protected static $effects = [0,0,2,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_BODY;
    protected $protection = 70;
    protected static $destroyed = null;
}	