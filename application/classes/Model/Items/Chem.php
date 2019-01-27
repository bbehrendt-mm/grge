<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Chem extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Eine Chemikalie',
			'icon' => 'chem/chem1',
			'description' => 'Dieses kleine Fläschchen enthält eine hochkomplizierte chemische Verbindung, die so cool ist, dass du ihren Namen nicht mal bei Wikipedia findest. Teste doch mal was passiert, wenn du das Zeug über irgendeinen deiner Gegenstände kippst, oder mit einer anderen Substanz mischt!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Farbige Substanz (Milosat)',		    'icon' => 'chem/chem1'),
			Array(	'name' => 'Farbige Substanz (Karmigol)',		'icon' => 'chem/chem2'),
			Array(	'name' => 'Farbige Substanz (Neotrigin)',		'icon' => 'chem/chem3'),
			Array(	'name' => 'Farbige Substanz (Limosuptin)',		'icon' => 'chem/chem4'),
			Array(	'name' => 'Farbige Substanz (Gatonoptium)',	    'icon' => 'chem/chem5'),
			Array(	'name' => 'Farbige Substanz (Betakosimtat)',	'icon' => 'chem/chem6'),

            Array(	'name' => 'Seltsame Substanz (Milodrin)',		'icon' => 'chem/chem1s'),
            Array(	'name' => 'Seltsame Substanz (Karmitain)',		'icon' => 'chem/chem2s'),
            Array(	'name' => 'Seltsame Substanz (Neotrigmat)', 	'icon' => 'chem/chem3s'),
            Array(	'name' => 'Seltsame Substanz (Limosuritat)',	'icon' => 'chem/chem4s'),
            Array(	'name' => 'Seltsame Substanz (Gatonoptigin)',	'icon' => 'chem/chem5s'),
            Array(	'name' => 'Seltsame Substanz (Betakosidatin)',	'icon' => 'chem/chem6s'),
	);

	protected static $weight = 0.2;
	
	public function __construct($target = null) {
        parent::__construct();
		
		if ($target === NULL)
            $this->type = random_int(0, 1) + (Globals::CurrentGameF()->config('modules.additionalchems') ? 1 : 0) * random_int(0,1) * 6;
		else $this->type = $target - 1;
	}
	
	
	public function chem_value() {
		return $this->type + 1;
	}

	public function mixchem($chemval): bool
    {
        $mixed = false;
        $ot = $this->chem_value();

        if ($chemval > 6 && $this->type > 5) {
            $this->type += ($chemval - 6);
            $d = $this->type - 11;
        } elseif ($chemval <= 6 && $this->type <= 5) {
            $this->type += $chemval;
            $d = $this->type - 5;
        } else {
            /** @noinspection NotOptimalIfConditionsInspection */
            if ($chemval > 6)
                $chemval -= 6;
            else
                $this->type -= 6;

            $this->type += $chemval;
            $d = $this->type - 5;
            $this->type += 6 * random_int(0,1);
            $mixed = true;
        }

        $this->consume();
		if ($d > 0 && !$mixed) {
            $damage = -20 - ($d - 1) * 9;
            $drunk = ($d - 1) * 20;

            Globals::CurrentPlayerF()->get_status()->modify(Model_Status::MS_STAT_HEALTH, $damage, Model_Status::MS_STAT_DRUNK, $drunk, Model_Status::MS_EFFECT_ITEM);

            Tool_Scripts::chem_reaction(
                'Du mischt beide Chemikalien zusammen. Mit einem Schlag gibt es einen lauten Knall, das Reagenzglas zerspringt und du findest dich in einer bestialisch stinkenden Wolke wieder. Diese beiden Stoffe zu mischen scheint keine allzu gute Idee gewesen zu sein...',
                $chemval, new Model_Items_Chem($ot)
            );

            return false;
        }
		if ($mixed) {
            $damage = -5 - ($d - 1) * 4;
            $radiation = ($d - 1) * 15;

            Globals::CurrentPlayerF()->get_status()->modify(Model_Status::MS_STAT_HEALTH, $damage, Model_Status::MS_STAT_RADIATION, $radiation, Model_Status::MS_EFFECT_ITEM);

            Tool_Scripts::chem_reaction(
                'Du mischt beide Chemikalien zusammen. Mit einem Schlag gibt es einen lauten Knall, das Reagenzglas zerspringt und du findest dich in einer bestialisch stinkenden Wolke wieder. Diese beiden Stoffe zu mischen scheint keine allzu gute Idee gewesen zu sein...',
                $chemval, new Model_Items_Chem($ot)
            );

            return false;
        }
        Tool_Scripts::chem_reaction(
            null,
            $chemval, new Model_Items_Chem($ot), new Model_Items_Chem($this->chem_value())
        );

        if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Du mischt beide Chemikalien zusammen. Es blubbert ein wenig, aber nachdem sich die Blasen gelegt haben stellst du fest, dass du soeben ein Fläschchen mit :result hergestellt hast! Herzlichen Glückwunsch!', array(':result' => [$this->name()])));
        return true;
	}
}	