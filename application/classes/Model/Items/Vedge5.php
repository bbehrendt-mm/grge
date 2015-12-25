<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Vedge5 extends Model_Combat_Weapons_Throwable implements Interface_Static {

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
                            ->effect(Model_Status::MS_STAT_ENERGY, 100)
                            ->effect(Model_Status::MS_STAT_SLEEPY, 100)
                            ->effect(Model_Status::MS_STAT_HEALTH, -85)
                            ->consume($this)
                            ->message('Nachdem du dieses Ding heruntergewürgt hast brennt dein Hals und dein Magen wie Feuer. Deinen Hunger oder Durst hat das nicht gestillt, aber zumindest bist du jetzt hellwach.')
                    )
            );
    }

	protected static $damage = [30,40];
	protected static $range = [1,18];
	protected static $accuracy = 0.9;
	protected static $use_fixed_accuracy = true;
	protected static $aoe = true;
	protected static $friendly_fire = false;
	protected static $energy = 1;
}	