<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Strangewood_Exit extends Model_Places_Abstract_Place implements Interface_Corridor {
	
	protected static $location_name = 'Parkplatz';
	protected static $description = 'Vor dem Eingang in den Wald findest du einen kleinen Parkplatz. Auf einigen Plätzen stehen verrostete Autos, deren Marken und Hersteller du noch nie gehört hast. Je tiefer du in den Wald blickst, desto weniger Licht siehst du durch die Baumkronen dringen. Bist du sicher, dass du weitergehen willst?';
    protected static $icon = 'exit';
    protected static $outside = true;
}	