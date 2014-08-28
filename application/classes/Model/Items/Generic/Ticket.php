<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Ticket extends Model_Items_Abstract_Item implements Interface_Autotaker, Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Geheimnisvolles Ticket',
			'icon' => 'ticket',
			'description' => 'Die Schrift auf diesem Ticket ist verblasst, nachdem es so lange der Sonnenstrahlung in der Aussenwelt ausgesetzt war. Was kann man mit diesem Ticket wohl machen?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_EVENT,
	);

	protected static $weight = 0;
}	