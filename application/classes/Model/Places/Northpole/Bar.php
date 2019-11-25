<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Bar extends Model_Places_Bar {
	
	protected static $namelist = Array('Schäbige Bar', 'Dunkle Kaschemme', 'Verfallene Kneipe');
	protected static $description = 'Eigentlich hat sich an diesem Ort mit der Zombieapokalypse nicht allzu viel geändert... an der Bar wird billiges, lauwarmes Bier getrunken und überall tummeln sich torkelnde, übelriechende Gestalten. Eigentlich ist der einzige Unterschied zu früher, dass einem nicht mehr nur die Brieftasche, sondern auch diverse innere Organe bei einem Besuch hier abhanden kommen könnten.';
    protected static $icon = 'bar';
    protected static $outside = false;

    protected static $temperature_engine = -10;
}	