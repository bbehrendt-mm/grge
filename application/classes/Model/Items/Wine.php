<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Wine extends Model_Items_Abstract_Alcohol implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Messwein',
			'icon' => 'wine',
			'description' => 'OK, für den Beichtvater und die Chorknaben konntest du nicht mehr allzuviel tun, aber wenigstens das Allerheiligste konntest du retten, als du aus der Kathedrale geflohen bist: den gesamten Messweinvorrat! Der wird dir helfen, über den Verlust deiner Gemeinde hinweg zu kommen. Waren sowieso alles Sünder...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 6;
	protected static $alcohol = 15;
}	