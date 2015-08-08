<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Machete extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Rostige Machete',
			'icon' => 'machete',
			'description' => 'Obwohl leicht abgestumpft und rostig ist diese Machete immer noch effektiv im Kampf gegen Zombiehorden. Noch besser ist es natürlich, die Zombies gar nicht erst in Machetenreichweite kommen zu lassen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	protected static $essential = true;

    protected static $damage = [5,8];
    protected static $energy = 5;
    protected static $max_range = 1;

	//public static $reload_time = 1;
	
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