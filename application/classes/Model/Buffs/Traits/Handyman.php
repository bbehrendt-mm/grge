<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Traits_Handyman extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Handwerker';
	protected static $icon = 'traits/handyman';
	protected static $desc = 'Du bist der König der Handwerker (zumindest seit Tim Allen von Zombies gefressen wurde). Durch deine Erfahrung kannst du Reparaturen und Versteck-Upgrades wesentlich effizienter durchführen und sparst dabei 10% Energie.';
	protected static $bid = 'tr_handyman';
}
