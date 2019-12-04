<?php defined('SYSPATH') OR die('No direct access allowed.');

//This is a generic building class that has multiple names; on is randomly selected when this class is constructed
class Model_Places_Northpole_Burgerjoint extends Model_Places_Burgerjoint {
	
	protected static $namelist = Array('"Rentier Roadkill" Restaurant', 'Wichtelige Fettstube', '"McFresskoma" Restaurant', '"Nordpol Wok" Restaurant');
    protected static $icon = 'np_restaurant';

    protected static $temperature_engine = 0;
    protected $temperature_scale     = 0.05;
    protected $temperature_deisolation = 0.25;
}	