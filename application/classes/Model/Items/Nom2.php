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
		global $player;
		
		$this->consume();
		if ($chemval < 2) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen auf! So ein Ärger, das wirst du wohl nicht mehr essen können...'));
			return false;
		} else {
			$player->location()->inventory()->add(new Model_Items_Nutrient);
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen in seine Bestandteile auf! Zurück bleibt nur eine große glibbrige Masse Nährschleim. Lecker ...'));
			return true;
		}
	}

}	