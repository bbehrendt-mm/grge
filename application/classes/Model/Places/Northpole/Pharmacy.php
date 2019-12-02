<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Pharmacy extends Model_Places_Pharmacy {

    protected static $namelist = Array('Apotheke "Hustensaft-Schlürfer"', 'Apotheke "Hypochonders bester Freund"', 'Alte Apotheke', 'Apotheke "Wehwehchen"');
	protected static $description = 'Die Tür dieser Apotheke ist von innen mit einem großen Blasentee-Werbeaufsteller verbarrikadiert. Leider hat das übergroße Schaufenster direkt daneben ausreichend Angriffsfläche für die Zombies geboten. Die Leute, die sich hier versteckt hatten, können mit den gelagerten Medikamenten wohl nichts mehr anfangen, also bedien dich ruhig.';
    protected static $icon = 'pharm';
    protected static $outside = false;

    protected static $temperature_engine = 0;
    protected $temperature_scale     = 0.05;
    protected $temperature_deisolation = 0.17;
}
