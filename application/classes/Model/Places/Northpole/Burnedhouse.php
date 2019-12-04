<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Burnedhouse extends Model_Places_Burnedhouse {
	
	protected static $location_name = 'Verbranntes Wichtelhaus';
    protected static $icon = 'np_burned';

    protected static $auto_doorways = array('bhouse');

    protected static $temperature_engine = 3;
}	