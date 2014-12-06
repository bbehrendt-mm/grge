<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Xmas_Drink extends Model_Items_Abstract_Alcohol implements Interface_Static, Interface_Event {
	
	protected static $static_info = Array(
			'name' => 'Weihnachtsgetränk',
			'icon' => 'xmas/mulled',
			'description' => 'Ein absolut klassisches Weihnachtsgetränk! Hauptsächlich deshalb, weil man sich im Dezember so viel davon reinschüttet, dass man es die restlichen 11 Monate nicht mehr anrühren kann.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

    protected static $instances_info = Array(
        Array(	'name' => 'Glühwein',
            'icon' => 'xmas/mulled',),
        Array(	'name' => 'Eierpunsch',
            'icon' => 'xmas/eggnogg')

    );
	
	protected static $weight = 2;
	protected static $alcohol = 5;
    protected static $thirst = 5;
    protected static $energy = 20;
    protected static $additional_effects = [
        Model_Player::MP_STAT_FREEZE => -18
    ];
}	