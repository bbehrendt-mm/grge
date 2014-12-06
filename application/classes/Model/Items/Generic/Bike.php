<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Bike extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Kaputtes Fahrrad',
			'icon' => 'bike',
			'description' => 'Ein simpler Drahtesel, mit dem du jederzeit überall hin kommst! Nur jetzt gerade nicht, denn es ist kaputt und muss repariert werden. Aber wenn du erstmal ein bisschen Arbeit reingesteckt hast wird es sich sicher lohnen!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 60;
    protected static $icon_ext = 'png';
    protected static $idea_contest_player = 'ExoticAsAlways';

}	