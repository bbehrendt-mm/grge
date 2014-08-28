<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Immune extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Immunisiert';
	protected static $icon = 'immune';
	protected static $desc = 'Du bist momentan davor geschützt, dich mit der Zombiekrankheit anzustecken. Genieße es, solange es anhält.';
	protected static $bid = 'immune';
}
