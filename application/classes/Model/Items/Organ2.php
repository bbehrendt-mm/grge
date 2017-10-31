<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Organ2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Generisches Tierorgan',
			'icon' => 'organ',
			'description' => 'Jeder Veterinär wird dir bestätigen können, dass es sich hierbei um ein generisches Organ handelt, welches sich an einer umspezifizierten Position in verschiedenen Tieren befindet und allgemeine Körperfunktionen übernimmt.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
            'deco' => -10
	);

	protected static $weight = 2;
}	