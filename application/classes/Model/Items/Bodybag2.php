<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bodybag2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Gebrauchter Leichensack',
			'icon' => 'bodybag2',
			'description' => 'Mit diesem Leichensack kannst du - Überraschung - Leichen transportieren. Schön verpackt ist so ein Körper viel einfacher zu transportieren, wenn du also auf Leichenjagd gehst solltest du so einen Sack immer dabei haben. Dieser Leichensack weist allerdings einige Gebrauchsspuren auf... er könnte beim Transport aufreißen, sei also vorsichtig.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);
	
	protected static $weight = 3;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Leiche einsacken', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->requirement(Model_Items_Body::cls(), 1)
                ->requirement(Model_Status::MS_STAT_ENERGY, 5)
                ->effect(
                    Model_Effect::factory()
                        ->consume($this)
                        ->spawn(Model_Items_Bodybag3::cls())
                        ->message('Du musst ein bisschen drücken, quetschen und pressen, aber irgendwann steckt diese Leiche komplett in deinem Leichensack. Jetzt kannst du sie viel einfacher transportieren, hurra!')
                , 'succ')
                ->effect(
                    Model_Effect::factory()
                        ->consume($this)
                        ->spawn(Model_Items_Bodybag4::cls())
                        ->spawn(new Model_Items_Body('Zerknautschte Leiche', 'Naja, die Form hat beim Transport im Leichensack etwas gelitten... Aber man erkennt, dass es mal so was ähnliches wie ein Mensch war!'))
                        ->message('Verflucht! gerade hast du die Leiche mühsam verstaut, reißt der Leichensack komplett auseinander! Transportieren kannst du damit nichts mehr...')
                , 'fail')
                ->decider(function() {
                    return (random_int(0, 9) <= 3) ? 'fail' : 'succ';
                })
                ->export('succ')
            );
    }
}	