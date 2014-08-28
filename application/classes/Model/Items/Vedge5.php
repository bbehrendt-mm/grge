<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Vedge5 extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Explosive Mutationsmelone',
			'icon' => 'vedge5',
			'description' => 'Genau DAS kommt dabei raus, wenn Mutter Natur von den Zombies total angepisst ist. Diese .... ähem ... "Frucht" hat einen ausgeprägten Selbsterhaltungstrieb und neigt bei Zombiekontakt zur Explosion. Du könntest sie auch essen... auch wenn das nicht unbedingt empfehlenswert ist.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 2;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_ENERGY, 100)
                            ->effect(Model_Player::MP_STAT_SLEEPY, 100)
                            ->effect(Model_Player::MP_STAT_HEALTH, -85)
                            ->consume($this)
                            ->message('Nachdem du dieses Ding heruntergewürgt hast brennt dein Hals und dein Magen wie Feuer. Deinen Hunger oder Durst hat das nicht gestillt, aber zumindest bist du jetzt hellwach.')
                    )
            );
    }
	
	protected static $distance_chance_modifier = 5;
	public static $range = Array(1,18);
	protected static $damage = Array(30,40);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_AREA;
	protected static $ammo = 'self';
	public static $accuracy = 0.9;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 1;

}	