<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Body2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Zerfetzter Zombie',
			'icon' => 'body2',
			'description' => 'Dieses Ding riecht noch gammeliger als gewöhnliche Leichen. Vermutlich sind es die Überreste eines Zombies, allerdings kann man das bei dieser Fleischpampe schwer sagen. Na, macht das Teil nicht Appetit?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
            'deco' => -100,
	);
	
	protected static $weight = 75;
	
	public function __construct($name = null, $desc = null) {
		parent::__construct();
		if ($name) $this->custom_info['name'] = $name;
		if ($desc) $this->custom_info['description'] = $desc;
	}

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 100)
                            ->effect(Model_Status::MS_STAT_HEALTH, -90)
                            ->effect(Model_Status::MS_STAT_ZOMBIFY, 10)
                            ->consume($this)
                            ->achieve(Model_Achievement::MA_BODY_EATER)
                            ->spawn(Model_Items_Generic_Bone3::cls())
                            ->message('Ohje, wer hätte das gedacht? Du hast dieses völlig verseuchte Stück Zombiefleisch runtergeschlungen und dich mit der Zombiekrankheit infiziert. Welch eine Überraschung!')
                    )
            );
    }
	
	public function mixchem($chemval) {
        switch ($chemval)
        {
            case 1:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie ätzt der Leiche das Fleisch von den Knochen! Es bleibt ledigtlich etwas Blut zurück... und ein perfekt erhaltenes Skelett!',
                    $chemval,$this, [new Model_Items_Generic_Bone3,new Model_Items_Generic_Waterb]);
                return true;
            case 8:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Der zerfetzte Körper saugt die Chemikalie auf. Es sieht aus, als würde sie ihn irgendwie desinfizieren...',
                    $chemval,$this, new Model_Items_Body('Desinfizierter Körper','Hurra, du hast es geschafft, einen Menschen von der Zombiekrankheit zu heilen... allerdings leider nur post mortem.'));
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Der zerfetzte Körper der Leiche saugt die Chemikalie auf, aber du kannst keine Veränderung feststellen ...',
                    $chemval,$this);
                return false;
        }
	}
}	