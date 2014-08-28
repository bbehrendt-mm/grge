<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Uniheal2 extends Model_Items_Abstract_Item {

	protected static $static_info = Array(
			'name' => 'Substanz H9CE42-X',
			'icon' => 'uniheal2',
			'description' => 'Dieses experimentelle Medikament wurde entwickelt, um die Zombieepidemie einzudämmen. Die Einnahme des Medikaments führt zu einer temporären Immunisierung vor der Zombieinfektion, hat jedoch bei einer bereits vorhandenen Infektion keinerlei Effekt.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);
	
	protected static $weight = 1;

    protected function hid() {
        return parent::hid()
            ->add_action('Applizieren', Model_Action::factory()
                    ->show_as(
                        Model_Effect::factory()
                            ->buff('Model_Buffs_Drug1', false, 144)
                            ->buff('Model_Buffs_Immune', false, 288)
                    )
                    ->decider(function($p) {
                        /** @var Model_Player $p */
                        return ($p->stats_get(Model_Player::MP_STAT_ZOMBIFY) > 0) ? 0 : 1;
                    })
                    ->effect(
                        Model_Effect::factory()
                            ->buff('Model_Buffs_Drug1', false, 144)
                            ->consume($this)
                            ->message('Du spritzt dir das Medikament... aber es scheint keine Wirkung zu haben!')
                    )
                    ->effect(
                        Model_Effect::factory()
                            ->buff('Model_Buffs_Drug1', false, 144)
                            ->buff('Model_Buffs_Immune', false, 288)
                            ->consume($this)
                            ->message('Du spritzt dir das Medikament... aber so wirklich passieren tut nichts. Tja, da wirst du wohl einfach hoffen müssen dass du nun immun bist.')
                    )
            );
    }
}	