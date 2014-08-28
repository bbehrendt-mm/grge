<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Rawmeat2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Gekochter Knochen mit Fleisch',
			'icon' => 'rawmeat2',
			'description' => 'Du hast diesen Knochen mit Fleisch so gnadenlos abgekocht, dass du ihn nun sogar halbwegs ohne schlechtes Gewissen verputzen kannst.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 4;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HUNGER, 16)
                            ->effect(Model_Player::MP_STAT_HEALTH, -1)
                            ->consume($this)
                            ->spawn('Model_Items_Bone')
                            ->message('Beim Kochen sind nicht nur die meisten Salmonellen, sondern auch fast alle Geschmacksstoffe verloren gegangen. Glücklicherweise handelt es sich hier um einen Knochen mit Fleisch, der Verlust von Geschmack ist also etwas sehr gutes...')
                    )
            );
    }

	public function mixchem($chemval) {
		global $player;

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