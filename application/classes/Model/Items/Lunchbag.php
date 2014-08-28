<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Lunchbag extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Lunchbag',
			'icon' => 'lunchbag',
			'description' => 'Es gibt nichts schöneres als liebevoll von Mutti bestrichene Brote. Gut, es war nicht deine eigene Mutter, und diese Tüte liegt seit Jahren hier in der Sonne, aber wir wollen doch jetzt nicht anfangen wählerisch zu werden, oder?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);	

	protected static $weight = 2;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HUNGER, 15)
                            ->consume($this)
                            ->message('Gierig schlingst du den Inhalt des Lunchbags herunter. Dein Hunger ist wieder etwas gestillt.')
                    )
            );
    }

	public function mixchem($chemval) {
		global $player;

		$this->consume();
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