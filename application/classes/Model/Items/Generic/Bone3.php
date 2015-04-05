<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Bone3 extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
        'name' => 'Angenagte Leiche',
        'icon' => 'bone3',
        'description' => 'Hier hat anscheinend jemand in der Nähe einer Leichen einen Fressanfall gehabt ...  oder du hast einfach die Überreste eines Magersüchtigen gefunden.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => -5,
	);

	protected static $weight = 50;
}	