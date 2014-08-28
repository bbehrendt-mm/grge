<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Cookieproto extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Teigkügelchen',
			'icon' => 'cookie_proto',
			'description' => 'Dieser von Elfen in weihnachtlicher Kinderarbeit hergestellte Teig ist alles, was du brauchst, um in Festtagsstimmung zu kommen. Und weil er von Elfen gemacht wurde ist er selbstständlich so magisch, dass sich aus ihm geformte Plätzchen automatisch selbst aufbacken. Wie praktisch!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 3;
}	