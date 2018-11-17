<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bmt extends Model_Items_Abstract_Item {

	protected static $static_info = Array(
			'name' => 'Bauchmuskeltrainer H.U.L.K.',
			'icon' => 'bmt',
			'description' => 'Du benötigst einen spontanen Kraftschub? Dann einfach Batterie einlegen und auf Start drücken! Dieser Bauchmuskeltrainer bringt dich garantiert auf Touren und ist dabei auch nur ein ganz kleines bisschen tödlich.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 5;
	protected static $essential = true;
	
	public $power = 0;
    protected static $energy_base = 30;
	public static $health_list = Array(3,5,8,13,21,34,55,89);
	
	public function description() {
		return  parent::description() . ($this->power >= count(static::$health_list) ? '<b>Die Kontakte dieses Exemplars sind leider komplett verkohlt... Dieses Ding wirst du wohl nicht mehr einsetzen können!</b>'
                : '');
	}

    protected function hid(): Model_Hid {
        return parent::hid()

            ->add_action('Einsetzen', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->requirement(Model_Items_Battery::cls(), 1)
                ->condition(function()  {
                    return ($this->power < count($this->health_list));
                })
                ->fail_message('Das kannst du nicht tun!')
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, ($this->power >= count($this->health_list)) ? -PHP_INT_MAX : -$this->health_list[$this->power])
                        ->effect(Model_Status::MS_STAT_ENERGY, static::$energy_base)
                        ->remove(Model_Items_Battery::cls(), 1)
                        ->custom(function() {
                            $this->power++;
                        }, Model_Effect::CFUNC_PROCESS_POST)
                        ->message('Uuuh, das hat gezwiebelt. Aber deine Energie ist wieder aufgeladen. Leider ist der Bauchmuskeltrainer jetzt etwas angekokelt...')
                )
            );
    }
	
	public function drop_dead() {
		return null;
	}
}	