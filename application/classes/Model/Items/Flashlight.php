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
    protected $on = true;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action(!$this->on ? 'Einschalten' : 'Ausschalten', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->effect(
                    Model_Effect::factory()
                        ->custom(function () {
                            $this->on = !$this->on;
                        })
                        ->message('Du hast den Schalter an der Taschenlampe betätigt.')
                )
            )
            ->add_action('Batterie wechseln', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->requirement(Model_Items_Battery::cls(), 1)
                ->effect(
                    Model_Effect::factory()
                        ->custom(function () {
                            $this->fillrate = 12;
                        })
                        ->message('Du hast eine neue Batterie in deine Taschenlampe eingelegt. Sie leuchtet nun wieder mit voller Kraft.')
                )
            )->add_action('Supercharger einlegen', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->requirement(Model_Items_Generic_Supercharger::cls(), 1)
                ->effect(
                    Model_Effect::factory()
                        ->consume($this)
                        ->spawn(Model_Items_Flashlight2::cls())
                        ->message('Du hast eine Supercharger-Batterie in diese Taschenlampe eingebaut. Mal sehen, was man aus diesem alten Teil noch alles rausquetschen kann!')
                )
            );
    }
	
	public function count() {
		return $this->fillrate;
	}

    public function icon() {
        return 'items/flashlight_' . ($this->active() ? 'on' : 'off');
    }

    public function active() {
        return ($this->fillrate > 0) && $this->on;
    }

    public function tick($pid, $type = Interface_Tickable::IT_TYPE_PLAYER) {
        if ($this->fillrate <= 0 || !$this->active())
            return;

        switch ($type) {
            case Interface_Tickable::IT_TYPE_PLAYER:
                if (Globals::CurrentGameF()->get_player($pid)) $this->fillrate--;
                break;
            case Interface_Tickable::IT_TYPE_NPC:
                if (Globals::CurrentGameF()->get_npc($pid)) $this->fillrate--;
                break;
        }
    }
}