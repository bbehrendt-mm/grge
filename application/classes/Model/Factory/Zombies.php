<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Factory_Zombies extends Model {

    private $config = 'default';
	private $type;
	private $population;
		
	public function __construct($assoc, $config = 'default') {
		$this->type = $assoc;
		$this->population = 0;
        $this->config = $config;
	}
	
	/**
	 * Calculates zombie accumulation
	 * @throws Exception
	 */
	public function accumulate_zombies($fixed = null) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
		global $game;
		
		if ($fixed === null) {
			//Get Config
            if (!$config = Tool_System::config_tree("spawn/{$this->config}", $this->type))
				throw new Exception('Failed to load zombie spawn configuration!');
			
			$this->population += (mt_rand(0, $config['accum'])/100) * (1 + 0.25 * ($game->duration() / (576/$game->config('zombies.accum'))));
		} else {
			$this->population += $fixed;
		}
	}
	
	/**
	 * Returns the number of accumulated zombies
	 * @return number
	 */
	public function get_zombie_accumulation() {
		return floor($this->population);
	}
	
	/**
	 * Resets the number of accumulated zombies
	 */
	public function reset_zombie_population() {
		$this->population = 0;
	}
	
	public function destroy_zombie_population($value) {
		$this->population -= $value;
		if ($this->population < 0) $this->population = 0;
	}
	
	/**
	 * Creates combatants from accumulated zombies and resets zombie counter
	 * @return null|Model_Battle_Combatant[]
	 */
	public function release_zombie_population() {
		
		if ($tmp = floor($this->population)) {
			$this->population = 0;
			return $this->spawn_zombies($tmp, true);
		} else return null;
	}

    /**
     * Returns an array with combating zombies from a single configuration case
     *
     * @param $data
     * @param int $multiply
     * @return null|Model_Battle_Combatant[]
     */
	private function dice($data, $multiply = 1) {
		//Failsafe
		if (!is_array($data) || count($data) == 0) return null;
		
		$ret = Array();
		foreach ($data as $instance) if (($num = round(mt_rand($instance["num"][0] * max(1, $multiply/2), $instance["num"][1] * max(1, $multiply)))) > 0)
			$ret[] = new $instance["type"]($num, mt_rand($instance["distance"][0], $instance["distance"][1]));
		
		return $ret;
	}

    /**
     * Returns an array with combating zombies, or null if there are no zombies to battle
     *
     * @param null $number
     * @param bool $strict
     * @throws Exception
     * @return null|Model_Battle_Combatant[]
     */
	public function spawn_zombies($number = null, $strict = false) {
        /**
         * @global $game Model_Game
         */
		global $game;
		
		//Get Config
		if (!$config = Tool_System::config_tree("spawn/{$this->config}", $this->type))
			throw new Exception('Failed to load zombie spawn configuration!');
		
		//No zombies appear
		if ($number === null && $config['chance'] < mt_rand(0, 100)) return null;
		
		//Spawn fixed number of zombies
		if ($number !== null) return Array(new Model_Battle_Shambler($number, $config['range']));
		
		if (count($config["groups"]) == 0) return null;
		
		//Calculate zombies
		return $this->dice($config["groups"][mt_rand(0, count($config["groups"]) - 1)], max(1,$game->duration() / 1440));
	}
	
	public function get_grouping_factor() {
        /**
         * @global $game Model_Game
         */
		global $game;
		if (!$config = Tool_System::config_tree("spawn/{$this->config}", $this->type))
			throw new Exception('Failed to load zombie spawn configuration!');

        if (($d = count($config["groups"])) == 0)
            return 0;

		$r = 0;
		$multiply = max(1,$game->duration() / 1440);
		foreach ($config["groups"] as $group)
			foreach ($group as $instance)
				$r += (($instance["num"][0] * max(1, $multiply/2) + $instance["num"][1] * max(1, $multiply))/2);
		
		return $r/$d;
	}
	
	public function get_appearcence_factor() {
        if (!$config = Tool_System::config_tree("spawn/{$this->config}", $this->type))
			throw new Exception('Failed to load zombie spawn configuration!');
		
		return $config['chance']/100;
	}
	
	public function get_accumulation_factor() {
        /**
         * @global $game Model_Game
         */
		global $game;
		
		//Get Config
		if (!$config = Tool_System::config_tree("spawn/{$this->config}", $this->type))
			throw new Exception('Failed to load zombie spawn configuration!');
		
		return ($config['accum']/200) * (1 + 0.25 * ($game->duration() / (576/$game->config('zombies.accum'))));
	}

}
