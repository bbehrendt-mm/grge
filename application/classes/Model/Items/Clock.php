<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Clock extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Kaputter Wecker',
			'icon' => 'clock',
			'description' => 'Gäbe es ein Ranking "Die 100 nutzlosesten Dinge in einer Apokalypse", dieses wäre vermutlich direkt hinter Joachim Gauck auf Platz 2. Naja, vielleicht kannst du ja zumindest ein paar Kleinteile ausbauen um den Event Countdown zu reparieren.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);
	
	protected static $weight = 3;

    protected function hid() {
        return parent::hid()
            ->add_action('Event-Countdown reparieren', Model_Action::factory()
                    ->condition(function($p) {
                        /** @var $p Model_Player */
                        return false;
                    })
                    ->requirement(Model_Player::MP_STAT_ENERGY, 4)
                    ->fail_message('Du hast den Countdown bereits repariert...')
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->achieve(Model_Achievement::MA_CLOCK)
                            ->message('Du hast in diesem Wecker tatsächlich ein paar nützliche Teile für den Countdown finden können. Jetzt funktioniert er wieder wie er soll! Herzlichen Glückwunsch!')
                    )
            );
    }
}	