<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Pseudoplayer {

	private $status_bars;
	private $inventory;

	final public function __construct($stats = []) {
		$this->status_bars = $stats;
		$this->inventory = new Model_Inventory();
	}

    /**
     * @return Model_Inventory
     */
    final public function inventory() {
		return $this->inventory;
	}

	final public function is_actual_player() {
		return false;
	}
	
	/**
	 * Changes players stats and rebuilds buffers afterwards
	 * @param number|array $args,... Supposed to be in this format: [stat1, change1, stat2, change2, ...]
	 * @throws Exception When $args is wrong format
	 */
	final public function stats_modify($args) {
		if (!is_array($args)) $args = func_get_args();
		
		//Make sure the input format is correct
		if (count($args) % 2) throw new Exception('Invalid input format for stat modificator!');
		
		//Run over each input pair
		$i = 0;
		while ($i < count($args)) {
			//Check if value is set already and calculate change
			if (!isset($this->status_bars[$args[$i]]))
                $this->status_bars[$args[$i]] = 0;

			$this->status_bars[$args[$i]] += $args[$i+1];
			
			//Min is 0
			$this->status_bars[$args[$i]] = max($this->status_bars[$args[$i]],0);
			
			//Jump to next pair
			$i += 2;
		}
	}
	
	/**
	 * Sets players stats (ignoring their previous values) and rebuilds buffers afterwards
	 * @param number|array $args,... Supposed to be in this format: [stat1, newval1, stat2, newval2, ...]
	 * @throws Exception When $args is wrong format
	 */
	final public function stats_set($args) {
		if (!is_array($args)) $args = func_get_args();
	
		//Make sure the input format is correct
		if (count($args) % 2) throw new Exception('Invalid input format for stat modificator!');
	
		//Run over each input pair
		$i = 0;
		while ($i < count($args)) {
			//Set new value
			$this->status_bars[$args[$i]] = $args[$i+1];
				
			//Enforce bounds (0/100)
			$this->status_bars[$args[$i]] = min(max($this->status_bars[$args[$i]],0),100);
				
			//Jump to next pair
			$i += 2;
		}
	}
	
	/**
	 * Returns a specific status value; if this value has not been set, returns 0
	 * @param int $stat
	 * @return int
	 */
	final public function stats_get($stat) {
		if (!isset($this->status_bars[$stat])) return 0;
        else return $this->status_bars[$stat];
	}
}
