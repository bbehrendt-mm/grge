<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Heatjacket1 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Santa-Kostüm',
			'icon' => 'heatsuit1',
			'description' => 'Sieht etwas lächerlich aus und schützt so gut wie gar nicht vor Zombie-Angriffen - dafür hält es dich aber auch vergleichsweise warm!',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 0;

    protected static $temperature_isolation_abs = 6.5;

    // INI, ATK, DEF, ACC
    protected static $effects = [0,0,0,0];

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_BODY;
    protected static $protection = 15;
    protected static $damage_reduction = 0;
    protected static $damage_blocking = 2;
    protected static $destroyed = 'Model_Items_Generic_Clothes';
}	