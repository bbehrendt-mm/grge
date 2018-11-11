<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bandage2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Improvisierte Bandage',
			'icon' => 'bandage2',
			'description' => 'Komm schon, bei diesem provisorisch mit Alkohol desinfizierten dreckigen Lappen ist die Infektionsgefahr auch nicht höher als in einem durchschnittlichen Krankenhaus. Außerdem gibt dir das Teil den total coolen Vietnam-Veteranen-Look.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);
	
	protected static $weight = 2;

    protected function hid() {
        $is_doc = !Globals::shadowPlayerExists() && Globals::PrimaryPlayerF()->job(10030);

        return parent::hid()
            ->add_action('Wunden versorgen',
                Model_Action::factory()
                    ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                    ->condition(function($p) {
                            /** @var Model_Player $p */
                            return (bool)$p->get_status()->retrieve('blood');
                        })
                    ->fail_message('So ein Teil solltest du dir nicht zum Spaß umlegen... wie wärs, wenn du wartest, bis du stark blutest?')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HEALTH, -35)
                            ->consume($this)
                            ->buff('Model_Buffs_Blood', true)
                            ->achieve(Model_Achievement::MA_BANDAGE_MUMMY)
                            ->message('Du wickelst die Bandage straff um deine Verletzungen. Zwar hast du dein Blut nun mit allerlei Dreck und Keimen geflutet, aber zumindest läuft es nicht mehr unkontrolliert aus deiner Wunde.')
                        )
            )
            ->add_action('Jmd. verbinden',
                Model_Action::factory()
                    ->allow_remote(false)
                    ->condition(function($p, $s) {
                        /** @var Model_Player $s */
                        return (bool)$s->get_status()->retrieve('blood');
                    })
                    ->fail_message('Auch wenn diese dreckige Bandage deinem Freund sicher gut stehen würde - warte lieber, bis er blutet.')
                    ->requirement(Model_Status::MS_STAT_ENERGY, 6)
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->message('Du wickelst die Bandage straff um die Verletzungen deines Freundes. Er ist zwar immer noch Leichenblass, das hat aber nichts mehr mit dem Blutverlust zu tun...')
                    , null, null,
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HEALTH, -24 + (!$is_doc ? 0 : (2 * Globals::PrimaryPlayerF()->job(false, null))))
                            ->buff('Model_Buffs_Blood', true)
                            ->achieve(Model_Achievement::MA_BANDAGE_MUMMY)
                            ->message(':name hat eine ziemlich schmutzige Bandage um deine Verletzungen gewickelt... wenigestens weist du jetzt was du ihm wert bist.', array(':name' => Globals::CurrentPlayerF()->name()))
                    )
            );
    }
}