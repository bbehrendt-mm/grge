<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('cursed_hideout')
            ->provide('hideout_slot')
            ->name('Verfluchtes Versteck')
            ->description('Bei diesem Versteck hast du ein ungutes Gefühl... zu recht!')
            ->category('Versteck')
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc_cursed')
            ->requires('cursed_hideout')
            ->name('Schaurige Küche')
            ->description('Die hier herumliegenden Küchenutensilien lassen dir einen kalten Schauer über den Rücken laufen...')
            ->category('Küche')
    )
    ->validate();