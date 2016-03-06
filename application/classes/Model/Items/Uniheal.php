<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Uniheal extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Substanz H9CE42-D',
			'icon' => 'uniheal',
			'description' => 'Dieses experimentelle Medikament wurde entwickelt, um die Zombieepidemie einzudämmen. In ersten Tests hat es sich als extrem wirksam in frühen Infektionsstadien erwiesen, allerdings kann es Zombies nicht wieder in Menschen verwandeln.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);
	
	protected static $weight = 1;

    protected function hid() {
        return parent::hid()
            ->add_action('Applizieren', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->allow_remote(false)
                ->show_as(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_ZOMBIFY, -PHP_INT_MAX)
                        ->buff('Model_Buffs_Drug1', false, 288)
                )
                ->decider(function($p) {
                    /** @var Model_Player $p */
                    return ($p->get_status()->get(Model_Status::MS_STAT_ZOMBIFY) > 0) ? 1 : 0;
                })
                ->effect(
                    Model_Effect::factory()
                        ->buff('Model_Buffs_Drug1', false, 288)
                        ->consume($this)
                        ->message('Du spritzt dir das Medikament, aber nichts geschieht... könnte es sein, dass du überhaupt nicht infiziert warst?')
                )
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_ZOMBIFY, -PHP_INT_MAX)
                        ->buff('Model_Buffs_Drug1', false, 288)
                        ->consume($this)
                        ->message('Nachdem du dir das Medikament gespritzt hast fühlst du sofort, wie deine Menschlichkeit zurückkehrt. Herzlichen Glückwunsch, du hast die Zombiekrankheit erfolgreich überwunden!')
                )
            );
    }
}	