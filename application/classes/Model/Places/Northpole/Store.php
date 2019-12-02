<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Store extends Model_Places_Store {

    protected static $namelist = Array('Tante Emma Laden', 'Kleiner Discounter', 'Kleiner Markt', 'Mini-Markt', 'Kleines Geschäft', 'Laden');
	protected static $description = 'Dies ist die Gelegenheit für dich, das absolut billigste Zeug aus einer minimalen Auswahl von Gebrauchsgegenständen und Lebensmitteln zu ergattern! Die vernagelten (und schlecht geputzten) Schaufenster lassen jedoch erahnen, dass dieses Geschäft wohl in nächster Zeit nicht mehr öffnen wird. Vor dem Laden steht ein Marktwagen mit der Aufschrift "Vera Loewenhaupt & Söhne", unter dem eine Leiche liegt...';
    protected static $outside = false;
    protected static $icon = 'store';

    protected static $temperature_engine = 0;
    protected $temperature_scale     = 0.05;
    protected $temperature_deisolation = 0.18;
}	