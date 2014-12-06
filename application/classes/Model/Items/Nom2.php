<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Nom2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Superleckere Speise',
			'icon' => 'nom2',
			'description' => 'Diese leckere Speise ist zusätzlich noch perfekt gewürzt. Einen so verführerischen Gaumenschmauß dürfen nur die wenigsten Verdammten genießen... du bist nun einer von ihnen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 2;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HUNGER, 50)
                            ->buff('Model_Buffs_Nom', false, 72)
                            ->consume($this)
                            ->message('Superlecker! Es geht doch nichts über etwas Selbstgekochtes!')
                    )
            );
    }
	
	public function mixchem($chemval) {
        $this->consume();
        switch ($chemval)
        {
            case 1:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen auf! So ein Ärger, das wirst du wohl nicht mehr essen können...',
                    $chemval,$this);
                return false;
            default:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen in seine Bestandteile auf! Zurück bleibt nur eine große glibbrige Masse Nährschleim. Lecker ...',
                    $chemval,$this, new Model_Items_Nutrient);
                return true;
        }
	}

}	