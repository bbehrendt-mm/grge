<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Mapping extends Model_Buffs_Abstract_Fragile {
	
	protected static $name = 'Karte zeichnen';
	protected static $icon = 'mapping';
	protected static $desc = 'Du arbeitest gerade an einer Karte dieses Orts. Dies erfordert deine volle Konzentration - du kannst keine anderen Aktionen durchführen und diesen Ort nicht zwischendurch verlassen.';

	protected static $abortable = true;
    protected static $allow_npc_assoc = false;
	
	private $level;
	
	public function __construct($level, $lifetime) {
		$this->level = $level;
		parent::__construct(null, $lifetime);
	}
	
	protected function action_on_complete() {
        /** @var Model_Items_Maptool $item */
        $item = Tool_Scripts::first_available_item('Model_Items_Maptool', true, false, false, $this->assoc_player);
		if (!$item) $this->assoc_player->log()->add('Das Kartographieren dieses Orts ist fehlgeschlagen...');
		else $item->score($this->level);
	}

}