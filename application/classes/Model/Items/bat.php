<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bat extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Baseballschläger',
			'icon' => 'bat',
			'description' => 'Ein ganz normaler Baseball-Schäger, so wie ihn die meisten Kinder sowie Ku-Klux-Klan-Mitglieder in Amerika besaßen. Du kannst damit nun Homerun-Rekorde oder Zombie-Schädel brechen - was sich eben gerade anbietet.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 6;

	public static $range = Array(0,1);
	protected static $damage = Array(3,6);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 0.8;
	public static $bounce = 1;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 3;
	
	public function mixchem($chemval) {
		global $game, $player;

        $player->log()->add('Sofort als die Chemikalie auf das Holz trifft beginnt sie, zu blubbern und zu zischen. Scheinbar reagiert sie mit dem Lack auf dem Schläger.... und löst das Holz auf. Tja, das war einmal ein Baseballschläger.');
        $player->location()->inventory()->add(new Model_Items_Generic_Crwood());
        $player->location()->inventory()->add(new Model_Items_Generic_Crwood());

		return false;
	}

}	