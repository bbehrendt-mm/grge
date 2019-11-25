<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Halloween_Helmet2 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Verdorbener Kürbishelm',
			'icon' => 'hw19_helmet2',
			'description' => 'Du fühlst eine unangenehme Präsenz in diesem Helm... bist du sicher, dass du so etwas auf deinen Kopf setzten möchtest?',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 1;

    protected static $temperature_isolation_abs = 1.0;

    // INI, ATK, DEF, ACC
    protected static $effects = [1,1,3,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_HELMET;
    protected static $protection = 40;
}	