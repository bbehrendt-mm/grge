<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Fire extends Model_Items_Abstract_Item implements Interface_Static, Interface_Countable, Interface_Tickable, Interface_Tmpitem {
	
	protected static $static_info = Array(
        'name' => 'Kleines Feuer',
        'icon' => 'fire',
        'description' => 'Klein, aber fein; dieses Feuer wird dich zumindest ein wenig warm halten.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
        'deco' => 15,
	);

	protected static $weight = 0;

	protected $charges = 12;

    public function can_take(string &$message): bool {
        $message = 'Das wirst du nicht ohne Brandverletzungen aufheben können...';
        return false;
    }

    public function count() {
        return $this->charges;
    }

    public function tick($id, $type = Interface_Tickable::IT_TYPE_PLAYER): void {
        if ($type == Interface_Tickable::IT_TYPE_LOCATION) $this->charges--;

        if ($this->charges <= 0) $this->consume();
    }
}	