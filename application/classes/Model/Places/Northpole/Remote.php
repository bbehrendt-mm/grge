<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Remote extends Model_Places_Remote {
	
	protected static $namelist = Array('Zentrum des Verfallenen Dorfes');
	protected static $description = 'Dieses Dorf scheint schon vor der Zombie-Apokalypse verlassen worden zu sein...';
    protected static $icon = 'np_desert2';

    protected static $temperature_engine = -4;
}