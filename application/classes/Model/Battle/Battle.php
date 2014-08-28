<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Battle_Battle {
	
	private $log = Array(0 => Array());
	private $round = 0;
	
	private $cont_humans = Array();
	private $cont_zombies = Array();
	
	private $escapable;
	private $active = false;
	
	public function __construct($escapeable = true) {
		$this->escapable = $escapeable;
	}
	
	/**
	 * Saves a log entry for current round
	 * @param Interface_Message $entry
	 */
	private function log_entry($entry) {
		if (!isset($this->log[$this->round])) $this->log[$this->round] = Array();
		$this->log[$this->round][] = $entry;
	} 
	
	/**
	 * Checks if there are still combatants on both sides
	 * @return boolean
	 */
	private function check_activity() {
		return ($this->active = ((count($this->cont_humans) * count($this->cont_zombies)) > 0));
	}
	
	/**
	 * Spawns a new combatant in battle
	 * @param Model_Battle_Combatant $combatant
	 */
	public function spawn_combatant($combatant) {			
		if (!$combatant->is_zombie()) $this->cont_humans[] = $combatant;
		else $this->cont_zombies[] = $combatant;
		
		$this->log_entry(new Model_Log_Types_Battle_Enter($combatant));
		$this->check_activity();
	}
	
	/**
	 * Returns true if the human combatants could escape
	 * @return boolean
	 */
	private function evasion() {
		$dst = 100;
		$ev = 1;
		foreach ($this->cont_zombies as $zombie) $dst = min($dst, $zombie->distance());
		foreach ($this->cont_humans as $human) $ev = min($ev, $human->evasiveness());
		
		return (mt_rand(0, 110) < ($dst * $ev * (Tool_Scripts::get_timeofday() == "night") ? 2 : 1));
	}
	
	/**
	 * Returns the group of zombies that is closest to the player
	 * @return Model_Battle_Combatant
	 */
	private function closest_zombie() {
		$ret = null;
		foreach ($this->cont_zombies as $zombie) if ($zombie->count() > 0) {
			if ($ret === null || $zombie->distance() < $ret->distance()) $ret = $zombie;
        }
		return $ret;
	}
	
	/**
	 * Returns the distance of the colsest zombie group
	 * @return number
	 */
	private function closest_distance() {
		return $this->closest_zombie()->distance();
	}
	
	/**
	 * Randomly selects a human combatant for the zombies to attack
	 * @return Model_Battle_Combatant
	 */
	private function select_human_victim() {
		if (count($this->cont_humans) == 0) return false;
        $did = mt_rand(0,count($this->cont_humans)-1);
        return isset($this->cont_humans[$did]) ? $this->cont_humans[$did] : false;
	}
	
	private function check_combatants() {
		foreach ($this->cont_humans as $id => $human) if (($human->count() + $human->health()) <= 0)
		{
			$this->log_entry(new Model_Log_Types_Battle_Death($human));
			unset($this->cont_humans[$id]);
		}
		foreach ($this->cont_zombies as $id => $zombie) if (($zombie->count() + $zombie->health()) <= 0)
		{
			$this->log_entry(new Model_Log_Types_Battle_Death($zombie));
			unset($this->cont_zombies[$id]);
		}
	}	
	
	public function fight() {
		$esc = $this->escapable && $this->evasion();
		$this->log_entry(new Model_Log_Types_Battle_Escape($this->escapable ? $esc : -1));
		
		if ($esc) foreach ($this->cont_humans as $human) $human->escape();
		
		$this->round++;
		
		if (!$esc) while ($this->tick()) $this->round++;

        foreach ($this->cont_humans as $human)
            /** @var Model_Battle_Combatant $human */
            $human->finalize();
        foreach ($this->cont_zombies as $zombie)
            /** @var Model_Battle_Combatant $zombie */
            $zombie->finalize();

        return $this->log;
	}
	
	/**
	 * Runs a round; returns weather the fight can go on
	 * @return boolean
	 */
	public function tick() {
		//Check battle state
		if (!$this->check_activity() || $this->round > 1280) return false;
		
		global $battle_log, $battle_round;
		$battle_log = $this->log;
		$battle_round = $this->round;

        //Randomize
        shuffle($this->cont_humans);
        shuffle($this->cont_zombies);

		//Humans act first
		foreach ($this->cont_humans as $human)
            $human->attack($this->cont_zombies);
		
		//Zombies act next
		foreach ($this->cont_zombies as $zombie)
			if (!$zombie->attack($this->cont_humans))
				$zombie->move();
		
		$this->log = $battle_log;
		
		$this->check_combatants();
		return $this->check_activity();
	}
	
	/**
	 * Returns zombie count
	 * @return number
	 */
	public function get_zombie_count() {
		$count = 0;
		foreach ($this->cont_zombies as $zombie) $count += $zombie->count();
		return $count;
	}
	
}