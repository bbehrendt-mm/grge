<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Gush extends Model_Combat_Weapons_Throwable {
	
	protected static $static_info = Array(
			'name' => 'Körpersaft-Drüse',
			'icon' => 'gush',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 0;

	protected static $damage = [1,3];
	protected static $range = [0,100];
	protected static $accuracy = 0.66;
	protected static $use_fixed_accuracy = true;
	protected static $aoe = false;
	protected static $friendly_fire = false;
	protected static $energy = 0;

    protected $int_capacity = 6;

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param number $damage
     * @param Model_Combat_Scene $scene
     * @return bool
     */
    public function trigger_usage(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene) {
        $tmp = parent::trigger_usage($me, $opponent, $damage, $scene);
        $this->int_capacity--;
        $this->usable = $this->int_capacity > 0;
        return $tmp;
    }

    public function generate_wound($damage) {
        if ($damage <= 0) return null;

        $injury = random_int(0,100);
        if ($injury < min(50,$damage * 10))
            return Model_Buffs_Poison::cls();
        else return null;
    }
}	