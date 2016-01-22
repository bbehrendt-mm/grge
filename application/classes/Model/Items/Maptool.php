<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Maptool extends Model_Items_Abstract_Item {

	protected static $static_info = Array(
			'name' => 'Kartographenausrüstung Marke "Glutspur"',
			'icon' => 'maptool',
			'description' => 'Dieses Sammlung nützlicher Dinge enthält alles, was du zum Kartographieren von Ruinen benötigst! ... nagut, es besteht aus einem Stapel Papier und einem Bleistift. Aber der ist immerhin spitz! Also hör auf dich zu beschweren!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);
	
	protected static $associated_view = 'mapper';
	protected static $weight = 0;
	protected static $essential = true;
	
	protected $information = 0;
	protected $datamem = Array(-2 => 3, -1 => 3);
	
	public function __construct() {
		$this->information = mt_rand(1,3);
		parent::__construct();
	}
	
	public function uin($new_uin = NULL) {
		return parent::uin($new_uin);
	}
	
	public function get_map_details() {
        /** @global Model_Player $player */
        global $player;
		$location = $player->location_class();
		if (!isset($this->datamem[$location])) return 0;
		else return $this->datamem[$location];
	}
	
	public function calc_duration($step) {
        /** @global Model_Player $player */
        global $player;
		
		$cfg = Array( 	0 => Array( 0 => Array(1 => 4, 2 => 7, 3 => 13), 1 => Array(1 => 7, 2 => 13), 2 => Array(1 => 10) ), 
						1 => Array( 0 => Array(1 => 3, 2 => 6, 3 => 12), 1 => Array(1 => 6, 2 => 12), 2 => Array(1 =>  9) ),
						2 => Array( 0 => Array(1 => 3, 2 => 5, 3 => 10), 1 => Array(1 => 5, 2 => 10), 2 => Array(1 =>  8) ),
						3 => Array( 0 => Array(1 => 2, 2 => 4, 3 =>  8), 1 => Array(1 => 4, 2 =>  8), 2 => Array(1 =>  8) ),
				);

		$level = $player->job(3020) ? $player->job(false, null) : 0;
		$current = $this->get_map_details();
		
		if (!isset($cfg[$level][$current][$step])) return -1;
		else return $cfg[$level][$current][$step];
	}
	
	public function common_discovery($value) {
		$this->information += $value;
	}

	public function score($p) {
        /** @global Model_Player $player */
        global $player;
		$location = $player->location_class();
		
		if (isset($this->datamem[$location]) && $this->datamem[$location] >= 3) return;
		if (!isset($this->datamem[$location])) $this->datamem[$location] = 0;
		
		$p = min(3 - $this->datamem[$location], $p);
		$this->datamem[$location] += $p;		

        if (Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Hideout')) $points = 1;
        elseif (Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Node')) $points = 5;
        else $points = 10;

        $this->information += $p * $points;
		
		switch ($p) {
			case 0: $player->log()->add(new Model_Log_Types_Text(null, null, 'Leider hast du deiner Karte keine neuen Informationen hinzufügen können...')); break;
			case 1: $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast ein paar zusätzliche Details in deine Karte aufgenommen.')); break;
			case 2: $player->log()->add(new Model_Log_Types_Text(null, null, 'Sehr schön! Du hast einige neue Informationen in deine Karte aufnehmen können!')); break;
			case 3: $player->log()->add(new Model_Log_Types_Text(null, null, 'Wow! Du hast diesen Ort bis ins kleinste Detail ausgekundschaftet und jedes einzelne Staubkorn in deine Karte gezeichnet.')); break;
		}
	}
	
	public function start_mapping($steps) {
        /**
         * @global Model_Player $player
         * @global Model_Game $game
         */
        global $player, $game;
		if ($player->get_status()->retrieve('fragile')) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Du bist momentan beschäftigt!'));
			return;
		}
		
		if ($this->calc_duration($steps) < 0) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Das kannst du momentan nicht tun!'));
			return;
		}
		
		if ($steps === true) {
			
			if (!$game->mass_consume(Array('Model_Items_Generic_Lasermapper' => 1))) {
				$player->log()->add(new Model_Log_Types_Text(null, null, 'Du scheinst kein Lasermessgerät dabei zu haben...'));
				return;
			}
			
			$steps = 1;
			if ($player->job(3030)) $steps++;
			if ($player->job(3030, 2, false)) $steps++;
			
			$this->score($steps);
		} else new Model_Buffs_Mapping($steps, $this->calc_duration($steps));
	}
	
	public function drop($p = null, $silent = false) {
		/** @global Model_Player $player */
		global $player;

		if (!$silent) $player->log()->add(new Model_Log_Types_Text(null, null, 'Das kannst du nicht ablegen...'));
		return false;
	}
	
	public function retrieve_info($clean = false) {
		$tmp = $this->information;
		if ($clean) $this->information = 0;
		return $tmp;
	} 
	
	public function drop_dead() {
		return null;
	}
}	