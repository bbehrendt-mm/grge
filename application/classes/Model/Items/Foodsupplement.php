<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Foodsupplement extends Model_Items_Abstract_Pillbox {

	protected static $static_info = Array(
			'name' => 'Schachtel mit Nahrungsergänzungsmitteln',
			'icon' => 'foodsupplement',
			'description' => 'Diese Nahrungsergänzungsmittel enthalten diverses hochkonzentriertes Zeug, das nach neuesten Forschungen der Marketingabteilung des Herstellers absolut lebensnotwendig und unverzichtbar ist. Jetzt kannst auch du 50€ für etwas zahlen, was du auch bekommen würdest, wenn du einfach in eine Kuh beißt.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);

	protected static $autospawn = Array(1,5);
    protected static $take_msg = 'Hurra, du hast deinen Hunger (ein bisschen) bekämpft und sogar noch etwas neue Energie erhalten.';
    protected static $singular_name = 'NEM';
    protected static $pill_effects = Array(
        Model_Status::MS_STAT_HUNGER => 5,
        Model_Status::MS_STAT_ENERGY => 2
    );
	
	public function mixchem($chemval): bool
    {
        switch ($chemval)
        {
            case 1:
                Tool_Scripts::chem_reaction(
                    'Die Pillen saugen die Chemikalie regelrecht auf! Vermutlich ast du jetzt ihre Wirkungsweise geändert...',
                    $chemval,$this, new Model_Items_Paracetin($this->count));
                $this->grind();
                return true;
            case 3:
                Tool_Scripts::chem_reaction(
                    'Die Pillen saugen die Chemikalie regelrecht auf! Vermutlich ast du jetzt ihre Wirkungsweise geändert...',
                    $chemval,$this, new Model_Items_Paracetoid($this->count));
                $this->grind();
                return true;
            case 5:
                Tool_Scripts::chem_reaction(
                    'Die Pillen saugen die Chemikalie regelrecht auf! Vermutlich ast du jetzt ihre Wirkungsweise geändert...',
                    $chemval,$this, new Model_Items_Paralaxium($this->count));
                $this->grind();
                return true;
            case 12:
                Tool_Scripts::chem_reaction(
                    'Die Pillen saugen die Chemikalie regelrecht auf! Wow, du hast Twinoid erzeugt!',
                    $chemval,$this, new Model_Items_Twinoid($this->count));
                $this->grind();
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie perlt von den Pillen ab... das hat wohl nichts gebracht.',
                    $chemval,$this);
                return false;
        }
	}
}	