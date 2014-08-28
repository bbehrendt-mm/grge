<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Nom extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Leckere Speise',
			'icon' => 'nom',
			'description' => 'Diese selbstgekochte Speise ist so lecker, dass du beim gedanken an sie direkt zu Sabbern anfängst. Etwas so gutes findet man nicht einfach irgendwo - man muss es sich selbst zusammenkochen!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 2;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HUNGER, 35)
                            ->buff('Model_Buffs_Nom', false, 36)
                            ->consume($this)
                            ->message('Lecker! Es geht doch nichts über etwas Selbstgekochtes!')
                    )
            );
    }
	
	public function mixchem($chemval) {
		global $player;

		$item->consume();
		if ($chemval > 3) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen auf! So ein Ärger, das wirst du wohl nicht mehr essen können...'));
			return false;
		} else {
			$player->location()->inventory()->add(new Model_Items_Nutrient);
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen in seine Bestandteile auf! Zurück bleibt nur eine große glibbrige Masse Nährschleim. Lecker ...'));
			return true;
		}
	}

}	