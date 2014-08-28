<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Abstract_Wbgun extends Model_Battle_Weapon implements Interface_Fillable, Interface_Countable {
	
	private $fillrate;
	protected static $capacity = 0;
	
	public function has_ammo() {
		return ($this->fillrate > 0);
	}
	
	public function consume_ammo() {
		$this->fillrate--;
	}
	
	public function interaction_fillfrom($item) {
        /**
         * @global $player Model_Player
         */
		global $player;
	
		if ($this->fillrate() >= static::$capacity) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Der Wasserbehälter dieser Waffe ist leider voll...'));
			return false;
		}	
		
		if (Tool_System::instance_of($item, 'Model_Items_Abstract_Bottle')) {
			/** @var $item Model_Items_Abstract_Bottle */
            if ($item->get_water(1)) $this->fillrate+= 10;
			else $player->log()->add(new Model_Log_Types_Text(null, null, 'Leider ist in dieser Flasche nicht mehr genug Wasser, um diesen Gegenstand zu füllen ...'));

            return true;
		} else return false;
	}
	
	public function interaction_fill($item) {
        /**
         * @global $player Model_Player
         */
		global $player;
	
		if ($this->fillrate() >= static::$capacity) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Der Wasserbehälter dieser Waffe ist leider voll...'));
			return false;
		}

		if (Tool_System::instance_of($item, 'Model_Items_Abstract_Liquid')) {
			/** @var $item Model_Items_Abstract_Liquid */
            $this->fillrate += 10;
			$item->consume();
		} else return false;
        return false;
	}
	
	public function capacity() {
		return static::$capacity;
	}
	
	public function fillrate() {
		return ceil($this->fillrate/10);
	}

    public function count() {
        return $this->fillrate;
    }

    protected function hid() {
        return parent::hid()
            ->add_action('Füllen oder Leeren...', Model_Action::factory()
                    ->javascript(
                        Model_Javascript::factory()
                            ->close_qtip()
                            ->versa('water')
                    )
            );
    }
}	