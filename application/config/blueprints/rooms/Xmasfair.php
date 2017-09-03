<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('manu_northpole')
            ->requires_room('invalid')
            ->name('Werkbank des Weihnachtsmanns')
            ->emplaces_action()
    );