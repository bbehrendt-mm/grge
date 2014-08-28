<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bandage extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Bandage',
			'icon' => 'bandage',
			'description' => 'Lieber Arm dran als Arm ab; aber wenn der Arm nun schonmal ab ist kann man wenigstens eine Bandage drum machen. Eine Bandage kann nur verwendet werden wenn du weniger als 50 Gesundheit hast!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);
	
	protected static $weight = 2;

    protected function hid() {
        /** @global Model_Player $player */
        global $player;
        return parent::hid()
            ->add_action('Wunden versorgen',
                Model_Action::factory()
                    ->condition(function($p) {
                            /** @var Model_Player $p */
                            return $p->stats_get(Model_Player::MP_STAT_HEALTH) <= 50 || (bool)$p->buff_retr('blood');
                        })
                    ->fail_message('Für die paar Kratzer willst du eine Bandage verwenden? Sei mal nicht so ein Schwächling, und warte zumindest bis deine Gesundheit auf 50 gefallen ist.')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HEALTH, 35)
                            ->consume($this)
                            ->buff('Model_Buffs_Blood', true)
                            ->achieve(Model_Achievement::MA_BANDAGE_MUMMY)
                            ->message('Du wickelst die Bandage straff um deine Verletzungen. Nach ein paar Minuten ist die Blutung gestillt und es geht dir besser.')
                        )
            )
            ->add_action('Jmd. verbinden',
                Model_Action::factory()
                    ->condition(function($p, $s) {
                        /** @var Model_Player $s */
                        return $s->stats_get(Model_Player::MP_STAT_HEALTH) <= 50 || $s->buff_retr('blood');
                    })
                    ->fail_message('Du machst dir zuviel Sorgen... bei ein paar kleinen Kratzern wäre eine Bandage doch wohl etwas übertrieben. Warte bis die Gesundheit deines Freundes unter 50 gefallen ist.')
                    ->requirement(Model_Player::MP_STAT_ENERGY, 6)
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->message('Du wickelst die Bandage straff um die Verletzungen deines Freundes. Mit den Bandagen im Gesicht sieht er gleich viel besser aus...')
                    , null, null,
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HEALTH, 50 + ($player->job(10030) ? 2 : 0) * $player->job(false, null))
                            ->buff('Model_Buffs_Blood', true)
                            ->achieve(Model_Achievement::MA_BANDAGE_MUMMY)
                            ->message(':name hat eine Bandage um deine Verletzungen gewickelt.', array(':name' => $player->name()))
                    )
            );
    }
}