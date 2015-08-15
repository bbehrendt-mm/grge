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

    public function updateConfigBase($config = 'default') {
        $this->config = $config;
    }

    /**
     * Returns the group size multiplier for this factory
     * @return number The return value is never smaller than 1
     */
    private function get_group_multiplier() {
        /** @global Model_Game $game */
        global $game;
        return max(1,$game->duration() / 1440);
    }

    /**
     * Returns the accumulation size multiplier for this factory
     * @return number The return value is never smaller than 1
     */
    private function get_accumulation_multiplier() {
        /** @global Model_Game $game */
        global $game;
        return 1 + 0.25 * ($game->duration() / (576/$game->config('zombies.accum')));
    }

	/**
	 * Calculates zombie accumulation
	 * @param null|int $fixed
	 * @throws Exception
	 */
	public function accumulate_zombies($fixed = null) {
		if ($fixed === null) {
			//Get Config
            if (!$config = Tool_System::config_tree("spawn/{$this->config}", $this->type))
				throw new Exception('Failed to load zombie spawn configuration (' . $this->config . ')!' );
			
			$this->population += (mt_rand(0, $config['accum'])/100) * $this->get_accumulation_multiplier();
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
	 * @return null|Model_Combat_Zombies_Zombie[]
	 */
	public function release_zombie_population() {
		
		if ($tmp = floor($this->population)) {
			$this->population = 0;
			return $this->spawn_zombies($tmp);
		} else return null;
	}

    /**
     * Returns an array with combating zombies from a single configuration case
     *
     * @param $data
     * @param int $multiply
     * @return null|Model_Combat_Zombies_Zombie[]
     */
	private function dice($data, $multiply = 1) {
		//Failsafe
		if (!is_array($data) || count($data) == 0) return null;
		
		$ret = Array();
		/** @var Model_Combat_Zombies_Ghul[] $instance */
		foreach ($data as $instance) if (($num = round($multiply * mt_rand($instance["num"][0], $instance["num"][1]))) > 0)
			$ret[] = $instance['type']::factory()->count($num)->set_distance(mt_rand($instance["distance"][0], $instance["distance"][1]));
		
		return $ret;
	}

	public function get_siege_range() {
		//Get Config
		if (!$config = Tool_System::config_tree("spawn/{$this->config}", $this->type))
			throw new Exception('Failed to load zombie spawn configuration (' . $this->config . ')!');

		return (int)$config['range'];
	}

	/**
	 * Returns an array with combating zombies, or null if there are no zombies to battle
	 *
	 * @param null $number
	 * @param bool $force
	 * @return Model_Combat_Zombies_Zombie[]|null
	 * @throws Exception
	 */
	public function spawn_zombies($number = null, $force = false) {
		//Get Config
		if (!$config = Tool_System::config_tree("spawn/{$this->config}", $this->type))
			throw new Exception('Failed to load zombie spawn configuration (' . $this->config . ')!');
		
		//No zombies appear
		if (!$force && $number === null && $config['chance'] < mt_rand(0, 100)) return null;
		
		//Spawn fixed number of zombies
        $ztype = empty($config["siege"]) ? 'Model_Combat_Zombies_Shambler' : $config["siege"];
		/** @var Model_Combat_Zombies_Ghul $ztype */
		if ($number !== null) return Array($ztype::factory()->count($number)->set_distance($config['range']));
		
		if (count($config["groups"]) == 0) return null;
		
		//Calculate zombies
		return $this->dice($config["groups"][mt_rand(0, count($config["groups"]) - 1)], $this->get_group_multiplier());
	}

    /**
     * Returns zombie radar data: Minimal group size, maximal group size, appearance probability per tick, average blocking increase per tick
     * @return array
     * @throws Exception When no spawn config exists
     */
    public function get_radar_data() {
        if (!$config = Tool_System::config_tree("spawn/{$this->config}", $this->type))
            throw new Exception('Failed to load zombie spawn configuration (' . $this->config . ')!');

        $sums_min = $sums_max = [];
        $m = $this->get_group_multiplier();
        foreach ($config['groups'] as $group) {
            $sum_min = $sum_max = 0;
            foreach ($group as $element) {
                $sum_min += round($element['num'][0] * $m);
                $sum_max += round($element['num'][1] * $m);
            }
            $sums_min[] = $sum_min;
            $sums_max[] = $sum_max;
        }

		if (empty($sum_min)) {
			$sums_min = [0];
			$sums_max = [0];
			$config['chance'] = 0;
		}

        return [
            min($sums_min), max($sums_max), $config['chance']/100, ($config['accum']/200) * $this->get_accumulation_multiplier()
        ];
    }
}
