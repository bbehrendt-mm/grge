<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Traits_Danger extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Zombiekundschafter';
	protected static $icon = 'traits/danger';
	protected static $desc = 'Die Präsenz von Zombies hinterlässt Spuren: Blutflecke, abgerissene Körperteile, CSU Wahlplakate.... Nur wenige Menschen verstehen jedoch so gut wie du, aus diesen Spuren Rückschlüsse auf die Zahl der Zombies zu ziehen! (Du kannst auch Geschlecht, Alter, Körpergröße und Lieblingsschokolade der Zombies bestimmen, aber das bringt leider überhaupt keinen Nutzen...). Dein Zombie-Radar zeigt somit wesentlich genauere Zombie-Abschätzungen an.';
	protected static $bid = 'tr_danger';

}
