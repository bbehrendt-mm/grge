<?php defined('SYSPATH') OR die('No direct access allowed.');

//This is a generic building class that has multiple names; on is randomly selected when this class is constructed
class Model_Places_Northpole_Weaponshop extends Model_Places_Weaponshop {
	
	protected static $location_name = 'Santas Waffenladen';
	protected static $description = 'Seit Santa seine Geschenke auch nach Brasilien und Mexiko ausliefert, boomt nicht nur die nordpolianische Waffenindustrie, sondern auch kleine Waffenverkaufsstellen wie diese.';
    protected static $icon = 'np_gun';

    protected static $temperature_engine = 0;
    protected $temperature_scale     = 0.05;
    protected $temperature_deisolation = 0.18;
}
