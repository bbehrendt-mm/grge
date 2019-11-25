<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Fire2 extends Model_Items_Generic_Fire {
	
	protected static $static_info = Array(
        'name' => 'Großes Feuer',
        'icon' => 'fire2',
        'description' => 'Dieses Feuer strahlt eine Menge Hitze ab - hier kannst du dich erst einmal aufwärmen.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
        'deco' => 25,
	);

    public function __construct($type = null)
    {
        parent::__construct($type);
        $this->charges = 60;
    }

}	