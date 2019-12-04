<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Burnedhouse2 extends Model_Places_Burnedhouse {
	
	protected static $location_name = 'Eingestürztes verbranntes Wichtelhaus';
	protected static $description = 'Vor dir liegt ein riesiger Haufen Schutt, den du mit viel Fantasie als die Reste eines verbrannten Hauses identifizieren kannst.';
    protected static $icon = 'np_collapse_burned';

    protected static $auto_doorways = array();

    protected static $temperature_engine = 3;
}	