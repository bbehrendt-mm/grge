<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Pseudoplayer implements Interface_Plentity {

	private $status_bars;
	private $inventory;
	private $location;
    private $status;

	final public function __construct($stats = [], $location_class = null) {
		$this->status_bars = $stats;
		$this->location = $location_class;
		$this->inventory = new Model_Inventory();
        $this->status = new Model_Status();
	}

    /**
     * @return Model_Inventory
     */
    final public function inventory() {
		return $this->inventory;
	}

	final public function is_actual_player() {
		return false;
	}

    public function get_status() {
        return $this->status;
    }

	final public function location_class() {
		/**
		 * @global Model_Player $player
		 */
		global $player;
		return ($this->location) ? $this->location : $player->location_class();
	}

	/**
	 * @return Model_Places_Abstract_Place|null
	 */
	final public function location() {
		/**
		 * @global Model_Game $game
		 * @global Model_Player $player
		 */
		global $game, $player;
		return ($this->location) ? $game->location($this->location) : $player->location();
	}

    public function kill() {}
}
