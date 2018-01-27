<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Braincoin extends Model_Items_Abstract_Ammo implements Interface_Autotaker {

    protected static $boni = [];

	protected static $static_info = Array(
			'name' => 'BrainCoin',
			'icon' => 'braincoin',
			'description' => 'Diese BrainCoins kannst du im ZombVival-Shop gegen nützliche Spielboni eintauschen. Im Spiel gefundene BrainCoins werden dir nur dann angerechnet, wenn du mindestens 24 Stunden überlebt hast und sich die BrainCoins zum Zeitpunkt deines Todes in deinem Munitionsgürtel befinden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_EVENT,
	);
	
	protected static $weight = 0;	
	protected static $autospawn = Array(3,9);
	protected static $autoappender = Array('BrainCoin', 'BrainCoins');
}	