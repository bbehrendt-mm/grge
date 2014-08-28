<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Battle_Ghul extends Model_Battle_Combatant {
	
	protected static $max_health = 100;
	protected $health = 1;
	protected static $max_energy = 0;
	protected $energy = 0;

	protected static $zombie_fraction = true;
	protected $distance;
	
	protected $lock = 0;
	protected static $speed = 10;
	
	protected static $unarmed = 'Model_Battle_Unarmed_Runner';
	
	private $log_pointer = null;
    protected $special = true;
	
	/**
	 * 
	 * @var Model_Inventory
	 */
	protected $inventory;
	protected $name;
	protected $count;
    private $pid;
	
	public function __construct($distance, $inventory, $name, $id, $health) {
		$this->health = $health;
        $this->pid = $id;
        parent::__construct($inventory, $distance, "[nt]$name", 1);
	}

    public function inventory() {
        return $this->inventory;
    }

    public function get_id() {
        return $this->pid;
    }
}