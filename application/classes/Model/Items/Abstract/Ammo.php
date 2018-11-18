<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Ammo extends Model_Items_Abstract_Stackable {
	
	protected static $boni = Array(1020 => Array(1 => 1, 2 => 1, 3 => 1, 4 => 1.15, 5 => 1.25, 6 => 1.25));

	public function __construct($num = null, $no_bonus = false) {
		parent::__construct($num);

        $bonus = 1;
        if (!$no_bonus && Globals::hasCurrentPlayer() && !Globals::shadowPlayerExists() && isset(static::$boni[Globals::PrimaryPlayerF()->job()])) {
            $lv = Globals::PrimaryPlayerF()->job(false);
            while ($lv > 0 && !isset(static::$boni[Globals::PrimaryPlayerF()->job()][$lv])) $lv--;
            $bonus = static::$boni[Globals::PrimaryPlayerF()->job()][$lv] ?? 1;
        }
        $this->count = ceil($this->count * $bonus);
	}
	
	public function take($silent = false): bool
    {
        return true;
	}
}	