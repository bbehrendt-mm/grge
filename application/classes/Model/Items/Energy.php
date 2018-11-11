<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Energy extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Energie (1 mA/h)',
			'icon' => 'energy',
			'description' => 'Energie wird von deinem Notstrom-Aggregat erzeugt und ist für diverse Ausbauten erforderlich. Außerdem kannst du damit Batterien in Supercarger-Batterien verwandeln..',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 0;	
	
	public function take($silent = false) {
		if (!$silent && !Tool_Scripts::is_npc()) Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Du kannst Energie nicht transportieren.'));
		return false;
	}
}	