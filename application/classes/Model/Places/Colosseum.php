<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Colosseum extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Kolosseum';
	protected static $description = 'Dieses alterwürdige Gebäude hat jahrhundertelang allen Kriegen und dem Zahn der Zeit widerstanden. Selbst die Zombieapokalypse konnte diesem Gebäude nichts anhaben. Heute wird es von einer geheimnissvollen Organisation als Austragungsort des Zombieturniers verwendet.';
    protected static $icon = 'colosseum';

	protected static $custom_style = 'colosseum';

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
				0 => Array('distance' => 100, 'zombies' => Array('Model_Combat_Zombies_Mutant' => 5)),
				//EASY BATTLES
				1 => Array('distance' => 100,	'zombies' => Array('Model_Combat_Zombies_Shambler' => 6)),
				2 => Array('distance' => 100,	'zombies' => Array('Model_Combat_Zombies_Runner' => 5)),
				3 => Array('distance' => 0,		'zombies' => Array('Model_Combat_Zombies_Fatass' => 1)),
				4 => Array('distance' => 30,	'zombies' => Array('Model_Combat_Zombies_Runner' => 1, 'Model_Combat_Zombies_Fatass' => 2)),
				5 => Array('distance' => 0,		'zombies' => Array('Model_Combat_Zombies_Mutant' => 10, 'Model_Combat_Zombies_Shambler' => 1)),
				//MEDIUM BATTLES
				6 => Array('distance' => 50,	'zombies' => Array('Model_Combat_Zombies_Shambler' => 15)),
				7 => Array('distance' => 30,	'zombies' => Array('Model_Combat_Zombies_Runner' => 5)),
				8 => Array('distance' => 0,		'zombies' => Array('Model_Combat_Zombies_Fatass' => 3)),
				9 => Array('distance' => 0,		'zombies' => Array('Model_Combat_Zombies_Runner' => 5)),
				10=> Array('distance' => 10,	'zombies' => Array('Model_Combat_Zombies_Mutant' => 20, 'Model_Combat_Zombies_Shambler' => 2)),
				//HARD BATTLES
				11=> Array('distance' => 100,	'zombies' => Array('Model_Combat_Zombies_Shambler' => 55)),
				12=> Array('distance' => 20,	'zombies' => Array('Model_Combat_Zombies_Runner' => 5, 'Model_Combat_Zombies_Fatass' => 3)),
				13=> Array('distance' => 0,		'zombies' => Array('Model_Combat_Zombies_Fatass' => 6)),
				14=> Array('distance' => 0,		'zombies' => Array('Model_Combat_Zombies_Runner' => 5, 'Model_Combat_Zombies_Shambler' => 5)),
				15=> Array('distance' => 100,	'zombies' => Array('Model_Combat_Zombies_Mutant' => 10, 'Model_Combat_Zombies_Shambler' => 10, 'Model_Combat_Zombies_Fatass' => 10, 'Model_Combat_Zombies_Runner' => 10)),
			);

	public function get_config() {
		return ($this->stage > 15) ? Array('distance' => 100, 'zombies' => Array('Model_Combat_Zombies_Behemoth' => $this->stage - 15)) : static::$stageconf[$this->stage];
	}

	private function battle(): void
    {
        $zmb = array();
        if ($this->stage > 15) $zmb[] = Model_Combat_Zombies_Behemoth::factory()->count($this->stage - 15);
        else foreach (static::$stageconf[$this->stage]['zombies'] as $z => $c)
            /** @var Model_Combat_Zombies_Zombie $z */
            $zmb[] = $z::factory()->count($c);

		Tool_Scripts::combat([Tool_Scripts::at_location($this->uin()), $zmb], false, ($this->stage > 15) ? 60 : static::$stageconf[$this->stage]['distance'], $this, $this->stage === 0 ? 'Der Qualifikationskampf im Kolosseum beginnt!' : ['Der Kampf auf Ebene :level des Kolosseums beginnt!', [':level' => $this->stage]]);
	}

    /**
     * @param $level
     *
     * @return Model_Items_Abstract_Item|null
     * @throws Exception
     */
	private function reward_roulette($level): ?Model_Items_Abstract_Item {
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
		
		return $rewards[$level][random_int(0, count($rewards[$level]) - 1)]();
	}
	
	private function reward(): void
    {
		if ($this->stage === 0) {

			$this->inventory->add(new Model_Items_Ammobelt);
			$this->inventory->add(new Model_Items_Machete);
			$this->inventory->add(new Model_Items_Batgun);
		}

		$c = ceil($this->stage / 3);
		for ($i = 1; $i <= $c; $i++) {
			$t = $this->reward_roulette($i);
			$this->inventory->add($t);
		}
	}
	
	public function level(): int
    {
		return $this->stage;
	}
	
	private function check_timer(): bool
    {
		if ($this->stage === 0 && Globals::CurrentGameF()->duration() > 288) {
            Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Ohje, du hast die Qualifikationsphase des Spiels verpasst. Jetzt kannst du nicht mehr am Turnier teilnehmen...'));
			return false;
		}
		if ($this->stage > 0 && (Globals::CurrentGameF()->duration() < (288 * ceil($this->stage/2)))) {
            Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Die Vorbereitungen für dieses Match laufen noch. Komm frühestens an Tag :day wieder.', array(':day' => 1+ceil($this->stage/2))));
			return false;
		}
		return true;
	}
	
	public function interaction_participate(): bool
    {
		if (!$this->check_timer()) return false;
		
		$this->battle();
		
		if (Globals::PrimaryPlayerF()->get_status()->alive()) {
            Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String('Du hast den Kampf überstanden!', 'Herzlichen Glückwunsch, du hast eine weitere Ebene des Kolosseums gemeistert! Weiter so! Als Belohnung für deinen triumphalen Sieg hast du einige Gegenstände erhalten.'));
			$this->reward();
			Tool_Scripts::home()->set_map_points($this->stage);
            Globals::PrimaryPlayerF()->achievements()->achieve(Model_Achievement::MA_GLADIATOR);
			$this->stage++;
		}
		

		return true;
	}

}	