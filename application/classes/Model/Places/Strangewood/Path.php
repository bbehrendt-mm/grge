<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Strangewood_Path extends Model_Places_Abstract_Trap implements Interface_Corridor {

    protected static $chance = 0.1;
	
	protected static $location_name = 'Trampelpfad';
	protected static $description = 'Dieser Pfad wird von unfassbar dichtem Gebüsch begrenzt - es scheint absolut unmöglich, von ihm abzuweichen.';
    protected static $outside = true;
}