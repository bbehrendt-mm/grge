<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Coffin extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Billiger Holzsarg',
			'icon' => 'coffin',
			'description' => 'Der Besitzer dieses Holzsargs hat bei seinem Begräbnis anscheinend gespart. Willst du nicht mal einen blick hinein riskieren? Möglicherweise hab man dem armen Tropf in dieser Kiste eine Beigabe in den Sarg gepackt ...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 300;
    protected static $energy = 8;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Öffnen', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->requirement(Model_Status::MS_STAT_ENERGY, static::$energy)
                ->effect(
                    Model_Effect::factory()
                        ->ambiguous_effect()
                        ->consume($this)
                        ->achieve(Model_Achievement::MA_GRAVEROBBER)
                        ->custom(function($p) {
                            /** @var Model_Items_Coffin $php53pb */
                            $this->open($p);
                        })
                )
            );
    }
	
	protected static $content = Array(
		Array('value' => 'Model_Items_Generic_Wood', 'chance' => 1),
		Array('value' => 'Model_Items_Generic_Metal', 'chance' => 1),
		Array('value' => 'Model_Items_Generic_Crwood', 'chance' => 1),
		Array('value' => 'Model_Items_Generic_Crmetal', 'chance' => 1),
		Array('value' => 'Model_Items_Battery', 'chance' => 4),
		Array('value' => 'Model_Items_Ammo', 'chance' => 1),
		Array('value' => 'Model_Items_Handgun', 'chance' => 1),
		Array('value' => 'Model_Items_Beer', 'chance' => 3),
		Array('value' => 'Model_Items_Whiskey', 'chance' => 2),
	);
	
	public function open($player = null) {
        if ($player === null)
            $player = Globals::CurrentPlayerF();
		
		$inset = null;
		$txt = 'Zum Glück ist der Sarg schon ziemlich verrottet, daher lässt er sich leicht öffnen. ';
		
		if (random_int(0, 100) < 40) {
			$txt .= 'Zu deiner Überraschung ist die Leiche im Sarg weniger tot als sie aussieht!';
			Tool_Scripts::simple_battle(1, 0, 'Der Leichnam im Sarg greift an!', true, false);
		} else {
			switch (random_int(0, 1)) {
				case 0:
					$txt .= 'Der im Sarg liegende Leichnam sieht noch ziemlich saftig aus... ';
					$player->location()->inventory()->add(new Model_Items_Body());
					break;
				case 1:
					$txt .= 'Der im Sarg liegende Leichnam sah bestimmt schonmal weniger blass aus... ';
					$player->location()->inventory()->add(new Model_Items_Generic_Bone3());
					break;
			}
			
			if (random_int(0, 100) > 40) {
                $classname = Tool_Gambling::roulette(static::$content);
				if (!class_exists($classname)) throw new LogicException("Item Class '$classname' is not valid!", 1);

                /**
                 * @var $classname string|Model_Items_Abstract_Item
                 * @var $item Model_Items_Abstract_Item
                 */
				$item = new $classname;
				$player->location()->inventory()->add($item);
				$txt .= 'Du hast Glück - neben den sterblichen Überresten findest du ein/eine/einen :item!';
				$inset = $item->name();
			} else $txt .= 'Leider scheint hier sonst nichts von Wert drin zu sein.';
		}
		
		if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, $txt, $inset ? array(':item' => [$inset]) : []));
		
		return true;
	}

}	