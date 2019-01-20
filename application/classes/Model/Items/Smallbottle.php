<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Smallbottle extends Model_Items_Abstract_Bottle {

	protected static $static_info = Array(
			'name' => 'Glasflasche',
			'icon' => 'smallbottle0',
			'description' => 'Sie ist weder groß noch sonderlich stabil, aber du kannst trotzdem ein wenig Flüssigkeit darin aufbewahren.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_BOTTLES,
	);

	protected static $weight = 0;
	
	protected static $capacity = 1;

    public function icon(): string {
        if ($this->fillrate() === 0)      return 'items/smallbottle0';
        else if ($this->fillrate() === 1) return 'items/smallbottle1';
        else return parent::icon();
    }
}	