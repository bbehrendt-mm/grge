<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Liquid extends Model_Items_Abstract_Item implements Interface_Tmpitem {
	
	protected $toxicity;
	protected static $weight = 0;

	public function __construct($toxicity = 0) {
		$this->toxicity = $toxicity;
		parent::__construct();
	}
	
	public function toxicity() {
		return $this->toxicity;
	}
	
	public function take($silent = false) {
		return false;
	}
}	