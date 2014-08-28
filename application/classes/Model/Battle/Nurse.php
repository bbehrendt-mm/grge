<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Battle_Nurse extends Model_Battle_Combatant {
	
	protected static $max_health = 4;
	protected $health = 4;
	protected static $max_energy = 0;
	protected $energy = 0;
	
	protected static $zombie_fraction = true;
	protected $distance;
	
	protected $lock = 0;
	protected static $speed = 2;
	
	protected static $unarmed = 'Model_Battle_Unarmed_Shambler';
	
	private $log_pointer = null;
	
	/**
	 * 
	 * @var Model_Inventory
	 */
	protected $inventory;
	protected $name;
	protected $count;
	
	public function __construct($count, $distance) {
		parent::__construct(new Model_Inventory(), $distance, 'Untote sexy Krankenschwester', $count);
	}
}