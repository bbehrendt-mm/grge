<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_ShopArchitective extends Model_Items_Abstract_Virtual {

    protected static $graceful_fail = true;

    protected static $default_action_uses = array(
        'hero_shop_architective' => 5,
    );

	protected static $static_info = Array(
			'name'          => 'Architektiv',
            'description'   => 'Deine herausragende Kombinationsgabe zusammen mit deinen weitreichenden Kentnissen der Architektur ermöglichen es dir, an vielen Orten Räume zu finden, die anderen verborgen geblieben wären.',
	);

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action("Architektonische Analyse", Model_Action::factory()
                ->buttonskin('hero_shop')
                ->description(static::static_description())
                ->condition(function(Model_Player $p) {
                    return $p->location()->user_room_available();
                })
                ->fail_message('Hier scheint sich kein weiterer nutzbarer Raum zu befinden ...')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function(Model_Player $p) {
                            $p->location()->setup_user_room();
                        })
                        ->message('Wow, du hast einen gut versteckten Raum entdeckt!')
                )
            , 'hero_shop_architective');
    }
}	