<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Whiskey extends Model_Items_Abstract_Alcohol implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Starker Alkohol',
			'icon' => 'whiskey',
			'description' => 'Ob es hilfreich ist, wenn du die Zombies zwar nicht mehr ganz so scharf, dafür aber doppelt siehst? Keine Ahnung, am besten du probierst es einfach mal aus!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Vodka',
					'icon' => 'vodka'),
			Array(	'name' => 'Whiskey',
					'icon' => 'whiskey'),
	);

    protected function hid() {
        /** @global Model_Player $player */
        global $player;
        return (!$player->job(10030)) ? parent::hid() : parent::hid()
            ->add_action('Jmd. Wunde auswaschen',
                Model_Action::factory()
                    ->condition(function($p, $s) {
                        /** @var Model_Player $s */
                        return (bool)$s->buff_retr('blood');
                    })
                    ->fail_message('Dein Freund hat keine Wunde, die du auswaschen könntest...')
                    ->requirement(Model_Player::MP_STAT_ENERGY, 15)
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->message('Ein bisschen Auswaschen, ein bisschen Eiter entfernen... schon sieht diese klaffende Wunde viel ansehnlicher aus.')
                        , null, null,
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HEALTH, -10)
                            ->effect(Model_Player::MP_STAT_DRUNK, 10)
                            ->buff('Model_Buffs_Blood', true)
                            ->message(':name hat deine Wunde mithilfe von Alkohol ausgewaschen.', array(':name' => $player->name()))
                    )
            );
    }

	protected static $weight = 5;
	protected static $alcohol = 30;
}	