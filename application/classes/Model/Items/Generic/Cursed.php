<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Cursed extends Model_Items_Abstract_Item {

	protected static $static_info = Array(
        'name' => 'Böser Teddy',
        'icon' => 'cursed_teddy',
        'description' => 'Diesem Teddy wurden die Augen herausgerissen und er ist mit Blut beschmiert! Normalerweise sind Teddies ja niedlich, aber DIESER HIER ...',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
        'deco' => -900,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Knuffibonkas, der böse Todesbär'),
			Array(	'name' => 'Unheiliges Stofftier'),
			Array(	'name' => 'Verfluchter Teddybär'),
			Array(	'name' => 'Befleckter Teddy der verlorenen Kindheit'),
	);
	
	protected static $weight = 2;    
    private $got_ack = false;
	
	public function take($silent = false) {
		global $game, $player;
		if (parent::take($silent))
		{
			if (!$this->got_ack) 
            {
                $this->got_ack = true;
                $player->achievements()->achieve(Model_Achievement::MA_HORROR);
            }
			return true;
		} else return false;
	}
}	