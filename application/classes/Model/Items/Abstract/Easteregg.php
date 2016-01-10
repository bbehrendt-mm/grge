<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Easteregg extends Model_Items_Abstract_Ammo {

	protected static $static_info = Array(
			'name' => 'Osterei',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_EVENT,
	);

	protected static $weight = 0;
    protected static $value = 0;
    protected $new = false;
	
	protected static $autospawn = Array(1,1);
	protected static $autoappender = Array('Ei', 'Eier');

    public static function getValue() {
        return static::$value;
    }

    public function __construct($num = null, $new = false) {
        $this->new = $new;
        parent::__construct($num);
    }

    public function take($silent = false) {
        if (parent::take($silent)) {
            $this->new = false;
            return true;
        } else return false;
    }
}	