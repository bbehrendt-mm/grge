<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Battle_Hallucination extends Model_Battle_Combatant {
	
	protected static $max_health = 5;
	protected $health = 5;
	protected static $max_energy = 0;
	protected $energy = 0;
	
	protected static $zombie_fraction = true;
	protected $distance;
	
	protected $lock = 0;
	protected static $speed = 5;
	
	protected static $unarmed = 'Model_Battle_Unarmed_Ghost';
	
	private $log_pointer = null;
	
	/**
	 * 
	 * @var Model_Inventory
	 */
	protected $inventory;
	protected $name;
	protected $count;
	
	public function __construct($count, $distance) {
		$names = array('Schnupfophanten', 'Jigsaw-Nudisten', 'Seehofer', 'Genitalmonster', 'Pokémon', 'Schwiegermütter');
        parent::__construct(new Model_Inventory(), $distance, $names[mt_rand(0,count($names)-1)], $count);
	}
}