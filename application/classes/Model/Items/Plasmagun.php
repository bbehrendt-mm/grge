<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Plasmagun extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Experimentelle Plasmakanone',
			'icon' => 'plasmagun',
			'description' => 'Dieser Waffe liegt ein faszinierendes Konzept zu grunde: Was, wenn man die Energie einer Batterie als Waffe nutzen würde, anstatt einfach die Batterie zu verschießen? Die experimentelle Plasmakanone benutzt richtig abgefahrene Wissenschaft sowie eine Batterie, um auf Plasmatemperatur erhitzte Luftpartikel in Richtung Zombies zu katapultieren. Wo diese Waffe hinfeuert bleibt kein Stein mehr auf dem anderen... allerdings kannst du sie aufgrund der extremen Hitzeentwicklung nur einmal pro Kampf einsetzen - und auch nur gegen Zombies, die weit entfernt stehen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 4;

	protected static $ammo = ['Model_Items_Battery' => 1];
	protected static $damage = [40,60];
	protected static $range = [20,100];
	//protected static $accuracy = 1;
	//protected static $use_fixed_accuracy = true;
	protected static $aoe = true;

	// INI, ATK, DEF, ACC
	protected static $effects = [20,0,0,0];

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_ENERGY;

	protected $used = false;

	public function usable(): bool {
		return !$this->used && parent::usable();
	}

	public function trigger_usage(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene): bool {
		parent::trigger_usage($me, $opponent, $damage, $scene);
		return ($this->used = true);
	}

	public function unregister(): Model_Combat_Weapon {
		parent::unregister();
		$this->used = false;
		return $this;
	}

}	