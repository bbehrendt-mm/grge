<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Water2 extends Model_Items_Abstract_Liquid implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Verseuchtes Wasser',
			'icon' => 'water_toxic',
			'description' => 'Dieses Wasser zu finden war nicht schwer - immerhin riecht man es Kilometer gegen den Wind. Um dieses ekelhafte schleimige Zeug zu trinken musst du schon sehr verzweifelt sein.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	public function __construct() {
		parent::__construct(Globals::CurrentGame()->config('items.water.tox_polluted'));
	}
}	