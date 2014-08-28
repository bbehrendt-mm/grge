<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Colosseum extends Model_Places_Abstract_Place {
	
	protected static $name = 'Kolosseum';
	protected static $description = 'Dieses alterwürdige Gebäude hat jahrhundertelang allen Kriegen und dem Zahn der Zeit widerstanden. Selbst die Zombieapokalypse konnte diesem Gebäude nichts anhaben. Heute wird es von einer geheimnissvollen Organisation als Austragungsort des Zombieturniers verwendet.';
	
	private $stage = 0;

	protected static $widget_list = Array(
			'colosseum',
			'description',
	);

    public function uin($uin = NULL) {
        if ($uin !== null)
            $this->inventory->add(new Model_Items_Virtual_Location_Coloseum());

        return parent::uin($uin);
    }

	private static $stageconf = Array( 
				0 => Array('distance' => 100, 'zombies' => Array('Model_Battle_Mutant' => 5)),
				//EASY BATTLES
				1 => Array('distance' => 100,	'zombies' => Array('Model_Battle_Shambler' => 6)),
				2 => Array('distance' => 100,	'zombies' => Array('Model_Battle_Runner' => 5)),
				3 => Array('distance' => 0,		'zombies' => Array('Model_Battle_Fatass' => 1)),
				4 => Array('distance' => 30,	'zombies' => Array('Model_Battle_Runner' => 1, 'Model_Battle_Fatass' => 2)),
				5 => Array('distance' => 0,		'zombies' => Array('Model_Battle_Mutant' => 10, 'Model_Battle_Shambler' => 1)),
				//MEDIUM BATTLES
				6 => Array('distance' => 50,	'zombies' => Array('Model_Battle_Shambler' => 15)),
				7 => Array('distance' => 30,	'zombies' => Array('Model_Battle_Runner' => 5)),
				8 => Array('distance' => 0,		'zombies' => Array('Model_Battle_Fatass' => 3)),
				9 => Array('distance' => 0,		'zombies' => Array('Model_Battle_Runner' => 5)),
				10=> Array('distance' => 10,	'zombies' => Array('Model_Battle_Mutant' => 20, 'Model_Battle_Shambler' => 2)),
				//HARD BATTLES
				11=> Array('distance' => 100,	'zombies' => Array('Model_Battle_Shambler' => 55)),
				12=> Array('distance' => 20,	'zombies' => Array('Model_Battle_Runner' => 5, 'Model_Battle_Fatass' => 3)),
				13=> Array('distance' => 0,		'zombies' => Array('Model_Battle_Fatass' => 6)),
				14=> Array('distance' => 0,		'zombies' => Array('Model_Battle_Runner' => 5, 'Model_Battle_Shambler' => 5)),
				15=> Array('distance' => 100,	'zombies' => Array('Model_Battle_Mutant' => 10, 'Model_Battle_Shambler' => 10, 'Model_Battle_Fatass' => 10, 'Model_Battle_Runner' => 10)),
			);
	
	public function get_config() {
		return ($this->stage > 15) ? Array('distance' => 100, 'zombies' => Array('Model_Battle_Behemoth' => $this->stage - 15)) : static::$stageconf[$this->stage];
	}

	private function battle() {
		global $game, $player;

        $zmb = array();
        if ($this->stage > 15) $zmb[] = new Model_Battle_Behemoth($this->stage - 15, 100);
        else foreach (static::$stageconf[$this->stage]['zombies'] as $z => $c) $zmb[] = new $z($c, static::$stageconf[$this->stage]['distance']);
        $battle_log = Tool_Scripts::battle($zmb, Tool_Scripts::at_location(), false, $battle, $zc);

		$this->log->add(new Model_Log_Types_Battle(($this->stage == 0) ? 'Der Qualifikationskampf im Kolosseum beginnt!' : 'Der Kampf auf Ebene :level des Kolosseums beginnt!', $battle_log, array(':level' => $this->stage)));
	}	
	
	private function reward_roulette($level) {
		
		$rewards = Array(
			1 => Array(
				0 => function() {return new Model_Items_Lunchbox(3);},
				1 => function() {return new Model_Items_Sportsdrink(3);},
				2 => function() {return new Model_Items_Battery(5);},
				3 => function() {return new Model_Items_Watergun();},
			),
			2 => Array(
				0 => function() {return new Model_Items_Money(3);},
				1 => function() {return new Model_Items_Paracetin(4);},
				2 => function() {return new Model_Items_Paracetoid(4);},
				3 => function() {return new Model_Items_Twinoid(2);},
				4 => function() {return new Model_Items_Phone();},
			),
			3 => Array(
				0 => function() {return new Model_Items_Watergun();},
				1 => function() {return new Model_Items_Batgun();},
				2 => function() {return new Model_Items_Machete();},
				3 => function() {return new Model_Items_Bandage();},
			),
			4 => Array(
				0 => function() {return new Model_Items_Ammo(5);},
				1 => function() {return new Model_Items_Generic_Plasma();},
				2 => function() {return new Model_Items_Chainsaw();},
				3 => function() {return new Model_Items_Handgun();},
			),
			5 => Array(
				0 => function() {return new Model_Items_Money(20);},
				1 => function() {return new Model_Items_Twinoid(6);},
			),
			6 => Array(
				0 => function() {return new Model_Items_Generic_Cursed();},
			),
		);
		
		
		if ($level < 1) return null;
		if ($level > 6) $level = 6; 
		
		return $rewards[$level][mt_rand(0, count($rewards[$level]) - 1)]();
	}
	
	private function reward() {
		if ($this->stage == 0) {

			$this->inventory->add(new Model_Items_Ammobelt);
			$this->inventory->add(new Model_Items_Machete);
			$this->inventory->add(new Model_Items_Batgun);
		}
		
		for ($i = 1; $i <= ceil($this->stage / 3); $i++) {
			$t = $this->reward_roulette($i);
			$this->inventory->add($t);
		}
	}
	
	public function level() {
		return $this->stage;
	}
	
	private function check_timer() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
		global $game, $player;

		if ($this->stage == 0 && $game->duration() > 288) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Ohje, du hast die Qualifikationsphase des Spiels verpasst. Jetzt kannst du nicht mehr am Turnier teilnehmen...'));
			return false;
		} elseif ($this->stage > 0 && ($game->duration() < (288 * ceil($this->stage/2)))) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Die Vorbereitungen für dieses Match laufen noch. Komm frühestens an Tag :day wieder.', array(':day' => ceil($this->stage/2))));
			return false;
		}	
		return true;
	}
	
	public function interaction_participate() {
        /**
         * @global $player Model_Player
         */
		global $player;
		
		if (!$this->check_timer()) return false;
		
		$this->battle();
		
		if ($player->alive()) {
			$player->log()->add(new Model_Log_Types_Text('Kampf', 'Du hast den Kampf überstanden!', 'Herzlichen Glückwunsch, du hast eine weitere Ebene des Kolosseums gemeistert! Weiter so! Als Belohnung für deinen triumphalen Sieg hast du einige Gegenstände erhalten.'));
			$this->reward();
			Tool_Scripts::home()->set_map_points($this->stage);
            $player->achievements()->achieve(Model_Achievement::MA_GLADIATOR);
			$this->stage++;
		}
		

		return true;
	}

}	