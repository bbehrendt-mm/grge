<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Coloseum extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'next_battle' => PHP_INT_MAX
    );

    protected function hid() {
        $phpbb53 = $this;
        return parent::hid()->add_action('Den nächsten Kampf austragen!', Model_Action::factory()
                ->effect(Model_Effect::factory()
                    ->custom(function($p) {
                            /** @var Model_Player $p */
                            $p->location()->interact('participate', null);
                    })
                )
            , 'next_battle');
    }
}	