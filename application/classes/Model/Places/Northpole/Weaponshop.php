<?php defined('SYSPATH') OR die('No direct access allowed.');

//This is a generic building class that has multiple names; on is randomly selected when this class is constructed
class Model_Places_Northpole_Weaponshop extends Model_Places_Weaponshop {
	
	protected static $location_name = 'Waffenladen';
	protected static $description = 'Dieser Waffenladen war schon so oft das Ziel von Plünderern, dass hier kaum noch etwas zu holen ist. Glücklicherweise gibt es dafür aber auch kaum Zombies hier.';
    protected static $icon = 'gun';
    protected static $outside = false;

    protected static $temperature_engine = 0;
}
