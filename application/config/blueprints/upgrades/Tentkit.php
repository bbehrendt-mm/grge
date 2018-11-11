<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('instatent')
            ->provide('hideout_slot')
            ->name('InstaZELT™')
            ->description('Ein mobiles Zuhause für jede Situation!')
            ->material(Model_Items_Tentkit::cls(),1)
            ->decay(-100)
            ->category('Versteck')
    )

    ->pop_stack()
    ;