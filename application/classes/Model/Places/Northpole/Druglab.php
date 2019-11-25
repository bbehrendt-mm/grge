<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Druglab extends Model_Places_Druglab {
	
	protected static $location_name = 'Drogenlabor';
	protected static $description = 'In diesem heruntergekommenen Schuppen wurden jahrelang diverse Mittelchen mit eher kontroverser Wirkung produziert. Es ist immer noch einiges an Equipment da, das du sicher für irgendwas nutzen kannst. Leider haben auch die Zombies Gefallen an diesem Örtchen gefunden... allerdings weniger wegen dem Equipment, sondern eher wegen den wehrlosen Junkies, die sich hier herumtreiben.';
    protected static $icon = 'lab';
    protected static $outside = false;

    protected static $temperature_engine = -14;
}	