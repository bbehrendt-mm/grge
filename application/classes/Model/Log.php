<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log extends Model {
	private $logstore = [];
	
	public function __sleep() {return [];}
}	