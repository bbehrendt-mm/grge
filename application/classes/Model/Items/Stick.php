<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Stick extends Model_Combat_Weapons_Close implements Interface_Static {
    protected static $static_info = Array(
        'name' => 'Brüchiger Stock',
        'icon' => 'stick',
        'description' => 'Ein brüchiger Stock ist auf sehr viele verschiedene Arten nutzlos; du kannst damit nichts bauen, und wenn du damit auf Zombies losgehst wirst du dir sicher ein paar Bissabdrücke einfangen.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );

	protected static $weight = 3;

	protected static $damage = [0,1];
	protected static $energy = 1;
	protected static $max_range = 1;

	protected static $durabillity = 0.2;
	//public static $reload_time = 0;
}	