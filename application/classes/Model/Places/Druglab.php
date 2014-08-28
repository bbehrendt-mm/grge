<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Druglab extends Model_Places_Abstract_Place {
	
	protected static $name = 'Drogenlabor';
	protected static $description = 'In diesem heruntergekommenen Schuppen wurden jahrelang diverse Mittelchen mit eher kontroverser Wirkung produziert. Es ist immer noch einiges an Equipment da, das du sicher für irgendwas nutzen kannst. Leider haben auch die Zombies Gefallen an diesem Örtchen gefunden... allerdings weniger wegen dem Equipment, sondern eher wegen den wehrlosen Junkies, die sich hier herumtreiben.';
    protected static $icon = 'lab';
    protected static $outside = false;

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);
		
		$count = mt_rand(3,15);
		for ($i = 0; $i < $count; $i++) $this->inventory->add(new Model_Items_Drugpack());
		
		$this->inventory->add(new Model_Items_Vending(get_class($this), "Drogotron"));
        return $t;
	}
}	