<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_ShopEagleEye extends Model_Items_Abstract_Virtual {

    protected static $graceful_fail = true;

    protected static $default_action_uses = array(
        'hero_shop_eye' => 5,
    );

	protected static $static_info = Array(
			'name'          => 'Adlerauge',
            'description'   => 'Mithilfe deiner geschulten Augen bist du in der Lage, sofort ein seltenes Item aufzustöbern.',
	);

	protected function candidates(Model_Player $p): array {
        return array_keys( array_filter( $p->location()->item_factory()->get(), function(float $v, string $k) {
            return $v <= 0.1 && !Tool_System::instance_of($k, Model_Items_Abstract_Virtual::cls());
        }, ARRAY_FILTER_USE_BOTH));
    }

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action(static::static_name(), Model_Action::factory()
                ->buttonskin('hero_shop')
                ->description(static::static_description())
                ->condition(function(Model_Player $p) {
                    return count( $this->candidates($p) ) > 0;
                })
                ->fail_message('Auf den ersten Blick erkennst du, dass du hier nichts seltenes finden wirst.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function(Model_Player $p) {
                            $item = Tool_Gambling::select( $this->candidates($p) );
                            if ($item) Tool_Scripts::place_new_item(new $item);

                        })
                        ->message('Wow, du hast einen gut versteckten seltenen Gegenstand entdeckt!')
                )
            , 'hero_shop_eye');
    }
}	