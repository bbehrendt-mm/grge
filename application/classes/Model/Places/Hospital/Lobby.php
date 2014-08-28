<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Hospital_Lobby extends Model_Places_Abstract_Node {
	
	protected static $name = 'Eingangsbereich des Krankenhauses';
	protected static $description = 'Die Lobby sieht aus wie ein Schlachtfeld... überall ist Blut, viele Durchgänge sind notdürftig verbarrikadiert. Wenn du dich jetzt fragst, ob du weitergehen solltest: Die Antwort lautet NEIN!';
    protected static $icon = 'exit';
    protected static $outside = false;
}	