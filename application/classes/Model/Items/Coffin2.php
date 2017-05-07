<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Coffin2 extends Model_Items_Coffin implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Edler Sarg',
			'icon' => 'coffin2',
			'description' => 'Bestes mit Klavirlack bemaltes Holz, goldene Griffe, ein religiöses Emblem in der Mitte... dieser Sarg war sicher ziemlich teuer! Mit etwas Glück liegt im Sarg noch eine wertvolle Beigabe! Warum schaust du nicht mal nach?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 500;
    protected static $energy = 20;
	
	protected static $content = Array(
		Array('value' => 'Model_Items_Battery', 'chance' => 1),
		Array('value' => 'Model_Items_Ammo', 'chance' => 2),
		Array('value' => 'Model_Items_Handgun', 'chance' => 2),
		Array('value' => 'Model_Items_Beer', 'chance' => 1),
		Array('value' => 'Model_Items_Whiskey', 'chance' => 3),
		Array('value' => 'Model_Items_Generic_Motor', 'chance' => 5),
		Array('value' => 'Model_Items_Generic_Supercharger', 'chance' => 5),
		Array('value' => 'Model_Items_Generic_Electro', 'chance' => 5),
		Array('value' => 'Model_Items_Generic_Micropur', 'chance' => 5),
		Array('value' => 'Model_Items_Generic_Bedcof', 'chance' => 5),
	);
	
	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_MISC;

    public function open($player = null) {
        if ($player === null)
            $player = Globals::CurrentPlayer();
		
		$inset = null;
		$txt = 'Das Ding ist ganz schön fest verschlossen... beinahe so, als hätten die Angehörigen Angst vor einem Wiedersehen mit dem Verstorbenen gehabt. Nach einigen Krafakten gelingt es dir dann allerdings doch, den Sarg aufzubrechen. ';
		
		if (mt_rand(0, 100) < 20) {
			$txt .= 'Zu deiner Überraschung ist die Leiche im Sarg weniger tot als sie aussieht!';
			Tool_Scripts::simple_battle(1, 0, "Der Leichnam im Sarg greift an!", true, false);
		} else {
			switch (mt_rand(0, 1)) {
				case 0:
					$txt .= 'Der im Sarg liegende Leichnam sieht noch ziemlich saftig aus... ';
					$player->location()->inventory()->add(new Model_Items_Body());
					break;
				case 1:
					$txt .= 'Der im Sarg liegende Leichnam sah bestimmt schonmal weniger blass aus... ';
					$player->location()->inventory()->add(new Model_Items_Generic_Bone3());
					break;
			}
			
			if (mt_rand(0, 100) > 20) {
				$classname = Tool_Gambling::roulette(static::$content);
				if (!class_exists($classname)) throw new Exception("Item Class '$classname' is not valid!", 1);

                /** @var $item Model_Items_Abstract_Item */
				$item = new $classname;
				$player->location()->inventory()->add($item);
				$txt .= 'Du hast Glück - neben den sterblichen Überresten findest du ein/eine/einen :item!';
				$inset = $item->name();
			} else $txt .= 'Leider scheint hier sonst nichts von Wert drin zu sein.';
		}
		
		if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayer()->log()->add(new Model_Log_Types_Text(null, null, $txt, array(), $inset ? array(':item' => $inset) : array()));
		
		return true;
	}

}	