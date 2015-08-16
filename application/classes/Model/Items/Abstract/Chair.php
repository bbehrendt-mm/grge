<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Chair extends Model_Combat_Weapons_Close {
	
	protected static $static_info = Array(
			'name' => 'Beliebiger Stuhl',
			'icon' => 'generic_chair',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected function weapon_break(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene) {
		parent::weapon_break($me, $opponent, $damage, $scene);
		if ($this->registered_user)
			$this->registered_user->achievements()->achieve(Model_Achievement::MA_BROKEN_CHAIRS, 1);
	}
}	