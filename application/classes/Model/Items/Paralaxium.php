<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Paralaxium extends Model_Items_Abstract_Pillbox {

	protected static $static_info = Array(
			'name' => 'Schachtel mit Paralaxium',
			'icon' => 'paralaxium',
			'description' => 'Manchmal muss man einfach mal abschalten; Paralaxium hilft dir dabei. Mit nur ein paar Kapseln bist du selbst dann noch entspannt, wenn Zombies an deinem Kopf knabbern.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);

    protected static $autospawn = Array(1,5);
    protected static $take_msg = 'Direkt nachdem du die Paralaxium schluckst fallen dir langsam die Augen zu...';
    protected static $singular_name = 'Paralaxium';
    protected static $pill_effects = Array(
        Model_Status::MS_STAT_SLEEPY => -5
    );
	
	public function mixchem($chemval) {
        switch ($chemval)
        {
            case 6:
                Tool_Scripts::chem_reaction(
                    'Die Pillen saugen die Chemikalie regelrecht auf! Wow, du hast Twinoid erzeugt!',
                    $chemval,$this, new Model_Items_Twinoid($this->count));
                $this->grind();
                return true;
            case 12:
                Tool_Scripts::chem_reaction(
                    'Die Pillen saugen die Chemikalie regelrecht auf! Vermutlich ast du jetzt ihre Wirkungsweise geändert...',
                    $chemval,$this, new Model_Items_Foodsupplement($this->count));
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