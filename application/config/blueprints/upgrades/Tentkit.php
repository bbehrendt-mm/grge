<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('instatent')
            ->provide('hideout_slot')
            ->name('InstaZELT™')
            ->description('Ein mobiles Zuhause für jede Situation!')
            ->material('Model_Items_Tentkit',1)
            ->decay(-100)
            ->category('Versteck')
    )
    ;