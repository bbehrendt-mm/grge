<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bat extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Baseballschläger',
			'icon' => 'bat',
			'description' => 'Ein ganz normaler Baseball-Schäger, so wie ihn die meisten Kinder sowie Ku-Klux-Klan-Mitglieder in Amerika besaßen. Du kannst damit nun Homerun-Rekorde oder Zombie-Schädel brechen - was sich eben gerade anbietet.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 6;

	protected static $damage = [3,6];
	protected static $energy = 3;
	protected static $max_range = 1;

    protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_PUNCH;

	protected static $durabillity = 0.8;
	
	public function mixchem($chemval): bool
    {
        $this->consume();

        switch ($chemval)
        {
            case 11:case 12:
                Tool_Scripts::chem_reaction(
                    'Das Holz saugt die Chemikalie auf... es scheint, als hättest du im wahrsten Sinne des Wortes eine chemische Keule erzeugt!',
                    $chemval,$this, new Model_Items_Batc);
                $this->grind();
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Sofort als die Chemikalie auf das Holz trifft beginnt sie, zu blubbern und zu zischen. Scheinbar reagiert sie mit dem Lack auf dem Schläger.... und löst das Holz auf. Tja, das war einmal ein Baseballschläger.',
                    $chemval,$this, [new Model_Items_Generic_Crwood,new Model_Items_Generic_Crwood]);
                return false;
        }
	}

}	