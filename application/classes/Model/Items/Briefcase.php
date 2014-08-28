<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Briefcase extends Model_Items_Abstract_Item {

	protected static $static_info = Array(
			'name' => 'Transzendente Handtasche',
			'icon' => 'briefcase',
			'description' => 'Es ist unglaublich, was alles in diese Handtasche passt! Schminkspiegel, mit Glitzerzeug verziehrtes Handy, Hello-Kitty-Digitalkamera ... alles sofort griffig! Man weiß nie wan man sowas in der Postapokalypse mal brauchen kann!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 0;
	protected static $essential = true;
	
	public function drop() {
		global $game, $player;
	
		$player->log()->add(new Model_Log_Types_Text(null, null, 'Gehts noch? Welche Frau gibt denn bitte ihre Tasche aus der Hand?'));
		return false;
	}
	
	public function drop_dead() {
		return null;
	}
}	