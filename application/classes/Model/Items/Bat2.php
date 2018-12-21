<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bat2 extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Stachelschläger',
			'icon' => 'bat2',
			'description' => 'Aus einem allträglichen Sportinstrument hast du ein bizarres Mordinstrument gemacht. Das sagt eine Menge über deine Psyche aus... zum Glück wird sich niemand trauen, dir das ins Gesicht zu sagen, solange du diesen Schläger in der Hand hälst.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 6;

	protected static $damage = [10,12];
	protected static $energy = 3;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_PUNCH;

	protected static $durabillity = 0.8;
	
	public function mixchem($chemval): bool
    {
        $this->consume();
        Tool_Scripts::chem_reaction(
            'Sofort als die Chemikalie auf das Holz trifft beginnt sie, zu blubbern und zu zischen. Scheinbar reagiert sie mit dem Lack auf dem Schläger.... und löst das Holz auf. Tja, das war einmal ein Baseballschläger.',
            $chemval,$this, [new Model_Items_Generic_Crwood,new Model_Items_Generic_Crmetal]);
        return false;
	}

}	