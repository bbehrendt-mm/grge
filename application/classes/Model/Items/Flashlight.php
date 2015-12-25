<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Flashlight extends Model_Items_Abstract_Item implements Interface_Countable, Interface_Tickable {
	
	protected static $static_info = Array(
			'name' => 'Taschenlampe',
			'icon' => 'flashlight_off',
			'description' => 'Mit dieser Taschenlampe kannst du nun endlich auch Nachts nach Gegenständen suchen. Tagsüber verbessert sie deine Fundchance an schlecht beleuchteten Orten. Wenn du Spaß am Experimentieren hast, belade sie doch mal mit einer Supercharger-Batterie...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 3;
	public $fillrate = 0;

    protected function hid() {
        return parent::hid()
            ->add_action('Batterie wechseln', Model_Action::factory()
                    ->requirement('Model_Items_Battery', 1)
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function () {
                                $this->fillrate = 12;
                            })
                            ->message('Du hast eine neue Batterie in deine Taschenlampe eingelegt. Sie leuchtet nun wieder mit voller Kraft.')
                    )
            )->add_action('Supercharger einlegen', Model_Action::factory()
                    ->requirement('Model_Items_Generic_Supercharger', 1)
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->spawn('Model_Items_Flashlight2')
                            ->message('Du hast eine Supercharger-Batterie in diese Taschenlampe eingebaut. Mal sehen, was man aus diesem alten Teil noch alles rausquetschen kann!')
                    )
            );
    }
	
	public function count() {
		return $this->fillrate;
	}

    public function icon() {
        return "items/flashlight_" . ($this->fillrate > 0 ? 'on' : 'off');
    }

    public function active() {
        return ($this->fillrate > 0);
    }

    public function tick($pid, $player_tick = true) {
        /** @global Model_Game $game */
        global $game;

        if ($this->fillrate <= 0)
            return;

        if ($player = $game->get_player($pid))
            $this->fillrate--;
    }

    public function render($pid) {
        /** @global Model_Game $game */
        global $game;

        if ($player = $game->get_player($pid)) {
            if (Tool_Scripts::get_timeofday() != 'night' && !$player->location()->is_outside() && $player->register_temp('flashlight'))
                $player->get_status()->modify(Model_Status::MS_CHAR_LOCATION_SPAWNRATE, 0.2);
        }
    }
}