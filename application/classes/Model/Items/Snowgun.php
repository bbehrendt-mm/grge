<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Snowgun extends Model_Items_Abstract_Wbgun {
	
	protected static $static_info = Array(
			'name' => 'Schneewerfer',
			'icon' => 'snowgun',
			'description' => 'Zombies hassen Wasser - auch in gefrorener Form. Der Vorteil des Schneewerfers gegenüber einer normalen Wasserpistole ist die höhere Reichweite sowie der geringere Wasserverbrauch.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $capacity = 4;
    public static $ammo_icon = 'items/snowball';
    protected static $fillrate_multiplier = 20;

    protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_WATER;

	protected static $weight = 10;

	protected static $damage = [7,18];
	protected static $range = [1,50];
	protected static $use_fixed_accuracy = false;
	protected static $aoe = false;

	protected static $accuracy_downscale = 0.6;
}	