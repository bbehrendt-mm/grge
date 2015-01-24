<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Mapping extends Model_Buffs_Abstract_Fragile {
	
	protected static $name = 'Karte zeichnen';
	protected static $icon = 'mapping';
	protected static $desc = 'Du arbeitest gerade an einer Karte dieses Orts. Dies erfordert deine volle Konzentration - du kannst keine anderen Aktionen durchführen und diesen Ort nicht zwischendurch verlassen.';

	protected static $abortable = true;
	
	private $level;
	
	public function __construct($level, $lifetime) {
		$this->level = $level;
		parent::__construct(null, $lifetime);
	}
	
	protected function action_on_complete() {
		$items = $this->assoc_player->inventory()->get('Model_Items_Maptool');
		if (count($items) != 1) $this->assoc_player->log()->add('Das Kartographieren dieses Orts ist fehlgeschlagen...');
		else $items[0]->score($this->level);
	}

}