<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Vending2 extends Model_Items_Abstract_Item {
	
	protected static $static_info = Array(
			'name' => 'Pfandautomat',
			'icon' => 'vending2',
			'description' => 'Ein Pfandautomat - eine der wenigen Möglichkeiten, wie man im Ruhrpott noch auf legale Weise an Geld kommen kann. Verfüttere doch ein paar deiner vielen Flaschen an ihn (die aus Glas, nicht deine Mitspieler).',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

    protected static $weight = 120;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Flasche einwerfen', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->condition(function($p) {
                    /** @var $p Model_Player */
                    foreach (Tool_Scripts::get_items(Model_Items_Smallbottle::cls(), Struct_ScriptItemSource::default()->use_perspective($p)) as $bottle)
                        /** @var Model_Items_Smallbottle $bottle */
                        if ($bottle->fillrate() === 0) {
                            $bottle->grind();
                            return true;
                        }
                    return false;
                })
                ->fail_message('Du benötigst eine leere Flasche, die du hineinwerfen kannst.')
                ->effect(
                    Model_Effect::factory()
                        ->spawn(new Model_Items_Money(1))
                        ->message('Der Automat hat deine Flasche geschluckt und etwas Geld dafür ausgespuckt.')
                )
            );
    }
}