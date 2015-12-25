<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Holybook extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Heilige Schrift',
			'icon' => 'holybook',
			'description' => 'Wann immer du am Zustand der Welt verzweifelst kannst du aus dieser Heiligen Schrift Kraft schöpfen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 0;
	protected static $essential = true;
	
	public $nextuse = 0;

    protected function hid() {
        $php53pb = $this;
        return parent::hid()
            ->add_action('Kraft schöpfen', Model_Action::factory()
                    ->condition(function() use ($php53pb) {
                        /** @global Model_Game $game */
                        global $game;
                        return ($php53pb->nextuse <= $game->duration());
                    })
                    ->fail_message('Du kannst maximal einmal pro Stunde Kraft aus einem Gebet schöpfen!')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_ENERGY, 4, 11)
                            ->message('Du schließt die Augen, kniest nieder und spürst die göttliche Kraft, die durch deinen Körper fließt.')
                            ->custom(function() use ($php53pb) {
                                /** @global Model_Game $game */
                                global $game;
                                $php53pb->nextuse = $game->duration() + 12;
                            })
                    )
            );
    }
	
	public function drop_dead() {
		return null;
	}
}	