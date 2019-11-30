<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Plaza extends Model_Places_Plaza {
	
	protected static $namelist = Array('Rathausplatz', 'Platz des Himmlischen Friedens', 'Hasselbachplatz', 'Stadtpark', 'Alter Marktplatz', 'Museumsplatz', 'Messeplatz');
	protected static $description = 'Dieser Platz ist umgeben von Wohnhäusern und Geschäften. Hier war früher immer eine bunte Mischung von Menschen zu beobachten. Spielende Kinder, telefonierende Yuppies, schlendernde Senioren... Heute ist dieser Platz menschenleer, nur noch vereinzelte Zombies schlurfen durch die Gegend. Wenigstens kannst du jetzt fast ungestört shoppen gehen!';

    protected static $temperature_engine = -2;
}