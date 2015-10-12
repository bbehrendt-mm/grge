<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below can be produced indefinitely and require local facilities
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires('cursed_hideout')->requires('ktc_cursed')->category('Küche');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:body2')
            ->name('Zersägte Leiche')
            ->message('Du benutzt deine Machete, um die Leiche in kleine Stücke zu schneiden. Das macht sie zwar nicht genießbarer, aber zumindest handlicher.')
            ->energy(30)
            ->material(['Model_Items_Body' => 1])
            ->produces(['Model_Items_Fleshfood' => 12, 'Model_Items_Generic_Waterb' => 1])
            ->effect(
                Model_Effect::factory()
                    ->achieve(Model_Achievement::MA_BLOODSUCKER)
            )
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:fsoup')
            ->message('Nicht daran denken was du hier gerade tust... nicht daran denken was du hier gerade tust... nicht daran denken was du hier gerade tust...')
            ->material(['Model_Items_Fleshfood' => 6, 'Model_Items_Generic_Waterb' => 1])
            ->energy(8)
            ->produces(['Model_Items_Fleshsoup' => 1])
    )

    ->drop_stack();