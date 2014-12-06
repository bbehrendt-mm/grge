<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Roadtrip_Garage extends Model_Places_Abstract_Place {

    protected static $namelist = Array('"Pay\'n\'Spray" Autowerkstatt', 'Bobs Autobude', '"Zombiewagen" Vertragswerkstatt');
    protected static $description = 'Wenn dein Auto merkwürdige Geräusche macht, hast du entweder einen Motorschaden oder einen Zombie auf der Rückbank. Glücklicherweise findet sich immer eine Werkstatt wie diese in der Nähe, die deine Karre reparieren oder den Zombie fachmännisch (mit einem großen Schraubenschlüssel) entfernen können. Unglücklicherweise hat diese Werkstatt derzeit leider aus unerfindlichen Gründen geschlossen...';
    protected static $icon = 'garage';
    protected static $outside = false;

}	