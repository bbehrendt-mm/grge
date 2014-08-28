<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Rawmeat extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Knochen mit Fleisch',
			'icon' => 'rawmeat',
			'description' => 'An diesem Knochen hängt noch eine Menge Fleisch. Du könntest es essen... aber dafür müsstest du schon wirklich verzweifelt sein, oder?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 5;

    protected function hid() {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HUNGER, 15)
                            ->effect(Model_Player::MP_STAT_HEALTH, -10)
                            ->consume($this)
                            ->spawn('Model_Items_Bone')
                            ->message('Es schmeckt ein bisschen nach Hähnchen .... und zwar nach einem Hähnchen, dass 4 Wochen lang in der Sonne verwest ist!')
                    )
            );
    }
	
	public function mixchem($chemval) {
		global $game, $player;

		if ($chemval == 6) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Die Chemikalie ätzt das gesamte Fleisch weg! Nur der Knochen bleibt zurück...'));
			$player->location()->inventory()->add(new Model_Items_Bone);
			$this->consume();
			return true;
		} else {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Das Fleisch saugt die Chemikalie auf, aber du kannst keine Veränderung feststellen ...'));
			return false;
		}
	}
}	