<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Godsword extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Göttliches Schwert',
            'icon' => 'machete_god',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 0;

    protected static $damage = [PHP_INT_MAX,PHP_INT_MAX];
    protected static $energy = 0;
    protected static $max_range = 3;
    protected static $aoe = true;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SLASH_MULTI;

}	