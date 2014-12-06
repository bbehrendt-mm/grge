<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Paracetin extends Model_Items_Abstract_Pillbox {

	protected static $static_info = Array(
			'name' => 'Schachtel mit Paracetin',
			'icon' => 'paracetin',
			'description' => 'Paracetin macht selbst den schlaffesten Sack wieder munter. Die hochkonzentrierte Mischung aus Koffein und dem von unserer Marketingabteilung neu entwickelten Acclerin - gewonnen aus natürlichem Orangenextrakt - bewirkt ein sofortiges Auffüllen von Energiereserven!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);

	protected static $autospawn = Array(1,10);
    protected static $take_msg = 'Direkt nachdem du die Paracetin schluckst fühlst du, wie deine Kraft zurückkehrt.';
    protected static $singular_name = 'Paracetin';
    protected static $pill_effects = Array(
        Model_Player::MP_STAT_ENERGY => 5
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
            case 9:
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