<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Leash extends Model_Items_Abstract_Item  {
	
	protected static $static_info = Array(
			'name' => 'Hundeleine',
			'icon' => 'leash',
			'description' => 'Mit der Hundeleine kannst du verhindern, dass sich dein Vierbeiner an gefährlichen Zombiekämpfen beteiligt, die Zone wechselt oder dir deine Gegenstände wegfrisst.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 0;
    protected static $essential = true;

    protected $on = false;

    public function is_active() {
        return $this->on;
    }

    public function drop($p = null, $silent = false) {
        return false;
    }

    public function drop_dead() {
        return null;
    }

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action(!$this->on ? 'Anlegen' : 'Ablegen', Model_Action::factory()
                ->allow_auto(false)
                ->effect(
                    Model_Effect::factory()
                        ->custom(function () {
                            $this->on = !$this->on;
                        })
                )
            );
    }
}