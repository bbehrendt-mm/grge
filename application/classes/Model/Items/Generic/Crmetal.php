<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Crmetal extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Metallsplitter',
			'icon' => 'crmetal',
			'description' => 'Diese Metallsplitter sind alleine nicht viel Wert, aber wenn du genug davon sammelst kannst du daraus eventuell etwas Nützliches herstellen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 7;
}