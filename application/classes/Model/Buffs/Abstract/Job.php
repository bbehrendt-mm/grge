<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Buffs_Abstract_Job extends Model_Buffs_Abstract_Buff {
	
	protected static $namelist = Array('Lv1', 'Lv2', 'Lv3', 'Lv4', 'Lv5');
	protected static $desclist = Array('Lv1', 'Lv2', 'Lv3', 'Lv4', 'Lv5');
	
	protected $level;
	
	public function __construct($player_id = NULL, $level = 1) {
		$this->level = $level;
		parent::__construct($player_id, -1);
		
		$this->adjust();
	}
	
	abstract protected function adjust(): void;
	
	public function icon(): string
    {
		return 'buffs/' . static::$bid . '/' . $this->level;
	}

    public static function static_icon(): string
    {
        return 'buffs/' . static::$bid . '/' . 1;
    }

    /**
     * Returns the buff name
     *
     * @return string
     */
    public static function static_name(): string {
        return static::$namelist[0];
    }

    /**
     * Returns the buff name
     *
     * @return string
     */
    public static function static_description(): string {
        return static::$desclist[0];
    }

	public function name(): string
    {
		if (isset(static::$namelist[$this->level - 1]))
            return static::$namelist[$this->level - 1];
        else return static::$namelist[count(static::$namelist) - 1];
	}
	
	public function description(): string
    {
        if (isset(static::$desc[$this->level - 1]))
            return static::$desc[$this->level - 1];
        else return static::$desc[count(static::$namelist) - 1];
	}
	
}
