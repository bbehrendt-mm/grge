<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Store extends Model_Places_Store {

    protected static $namelist = Array('Der Elfen-Store', 'Billig-Discounter', 'Winziger Wichtelmarkt');
    protected static $icon = 'np_store';

    protected static $temperature_engine = 0;
    protected $temperature_scale     = 0.05;
    protected $temperature_deisolation = 0.18;
}	