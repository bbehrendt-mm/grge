<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bigbottle extends Model_Items_Abstract_Bottle {

	protected static $static_info = Array(
			'name' => 'Wasserkanister',
			'icon' => 'bigbottle0',
			'description' => 'Ein sehr praktischer Behälter, um größere Mengen Wasser zu transportieren.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_BOTTLES,
	);

	protected static $weight = 5;
	
	protected static $capacity = 12;

	public function icon(): string {
        if ($this->fillrate() === 0)        return 'items/bigbottle0';
        else if ($this->fillrate() < 6)     return 'items/bigbottle1';
        else if ($this->fillrate() < 12)    return 'items/bigbottle2';
        else if ($this->fillrate() === 12)  return 'items/bigbottle3';
        else return parent::icon();
    }
}	