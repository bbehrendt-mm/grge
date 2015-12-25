<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Flashlight2 extends Model_Items_Abstract_Item {
	
	protected static $static_info = Array(
			'name' => 'Instabile Taschenlampe',
			'icon' => 'flashlight2',
			'description' => 'Diese Taschenlampe wurde mit einer Supercharger-Batterie geladen. Niemand kann vorhersagen was passiert wenn du dieses Ding einschaltest. Möglicherweise vertreibt es in der Nähe stehende Zombies, vielleicht aber auch nicht...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 3;

    protected function hid() {
        $php53pb = $this;
        return parent::hid()
            ->add_action('Batterie entnehmen', Model_Action::factory()
                    ->fail_message('Der Tank ist zu voll, als dass er einen weiteren Kanister Benzin aufnehmen könnte.')
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->spawn('Model_Items_Flashlight')
                            ->spawn('Model_Items_Generic_Supercharger')
                            ->message('Dir war das Risiko, dieses Teil einzusetzen, offensichtlich zu hoch. Tja...')
                    )
            )->add_action('Einschalten', Model_Action::factory()
                    ->show_as(Model_Effect::factory()
                        ->ambiguous_effect()
                        ->consume($this)
                    , null, true)
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function($p) {
                                /** @var $p Model_Player */
                                $p->location()->zombie_pop(true);
                            })
                            ->message('Du schaltest die Taschenlampe ein, die sofort einen enormen Lichtblitz erzeugt. Als du deine Augen wieder öffnest stellst du fest, dass der Blitz alle Zombies vertrieben hat, die deinen Weg blockiert haben. Die Taschenlampe ist jedoch völlig zerstört.')
                    )
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HEALTH, -50)
                            ->buff('Model_Buffs_Blood')
                            ->message('Du schaltest die Taschenlampe an. Mit einem lauten Knall explodiert sie in deiner Hand und fügt dir schwere Verletzungen zu!')
                    )
                    ->effect(
                        Model_Effect::factory()
                            ->spawn('Model_Items_Generic_Metal')
                            ->message('Du schaltest die Taschenlampe ein. Zuerst geschieht anscheinend nichts... dann beginnt die Lampe, eine unglaubliche Hitze zu entwickeln. Erschrocken lässt du sie fallen, und vor deinen Augen zerschmilzt sie zu einem Metallklumpen.')
                    )
            );
    }
}	