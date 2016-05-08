<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Concrete extends Model_Combat_Weapons_Throwable implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Betonstücke',
			'icon' => 'concrete',
			'description' => 'Betonstücke - die einfachste Methode, jede Demonstration aus dem Ruder laufen zu lassen. Jetzt erhältlich in den Trendfarben Dunkelweiß, Grau und Hellschwarz.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 3;

	protected static $damage = [4,10];
	protected static $range = [0,30];
	protected static $accuracy = 0.65;
	protected static $use_fixed_accuracy = true;
	protected static $aoe = false;
	protected static $friendly_fire = false;
	protected static $energy = 2;

	/**
	 * @param Model_Combat_Actor $me
	 * @param Model_Combat_Actor $opponent
	 * @param number $damage
	 * @param Model_Combat_Scene $scene
	 * @return bool
	 */
	public function trigger_usage(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene) {
        if ($this->registered_user)
            $this->registered_user->achievements()->achieve(Model_Achievement::MA_ANONYMOUS);
		return parent::trigger_usage($me, $opponent, $damage, $scene);
	}
}	