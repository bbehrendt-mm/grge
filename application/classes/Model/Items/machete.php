<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Machete extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Rostige Machete',
			'icon' => 'machete',
			'description' => 'Obwohl leicht abgestumpft und rostig ist diese Machete immer noch effektiv im Kampf gegen Zombiehorden. Noch besser ist es natürlich, die Zombies gar nicht erst in Machetenreichweite kommen zu lassen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	protected static $essential = true;

	public static $range = Array(0,1);
	protected static $damage = Array(5,8);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 1;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 5;
	
	public function mixchem($chemval) {

        switch ($chemval)
        {
            case 4:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie läuft an der Klinge herunter und ätzt den Rost weg! Deine Machete ist nun schärfer den je!',
                    $chemval,$this, new Model_Items_Machete2);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie läuft an der Klinge herunter, doch nichts passiert ...',
                    $chemval,$this);
                return false;
        }
	}

}	