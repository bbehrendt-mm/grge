<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Pharmacy extends Model_Places_Pharmacy {

    protected static $namelist = Array('Apotheke "Hustende Huftiere"', 'Apotheke "Schniefende Schlittenzieher"', 'Apotheke "Paralysierte Paarhufer"', 'Apotheke "Fiebrige Fellträger"');
    protected static $icon = 'np_pharm';

    protected static $temperature_engine = 0;
    protected $temperature_scale     = 0.05;
    protected $temperature_deisolation = 0.17;
}
