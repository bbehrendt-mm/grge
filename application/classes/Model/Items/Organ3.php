<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Organ3 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Generisches Zombieorgan',
			'icon' => 'organ',
			'description' => 'Jeder Nekromant wird dir bestätigen können, dass es sich hierbei um ein generisches Organ handelt, welches sich an einer umspezifizierten Position im Zombie befindet und allgemeine Körperfunktionen übernimmt.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
            'deco' => -15
	);

	protected static $weight = 3;
}	