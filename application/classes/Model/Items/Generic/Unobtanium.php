<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Unobtanium extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
        'name' => 'Rohstoffe der Vergangenheit',
        'icon' => 'unobtanium',
        'description' => 'Diesen Rohstoff kannst du im Spiel nicht erhalten.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 0,
	);

	protected static $weight = 1000;

    /**
     * Item constructor
     * Will fail.
     */
    public function __construct($type = null) {
        parent::__construct($type);
        $this->grind();
        throw new Exception("Attempted to create unobtainable item!");
    }
}	