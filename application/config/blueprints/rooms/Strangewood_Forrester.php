<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('workshop_hw19_forrester')
            ->requires_room('invalid')
            ->requires_room_tag('inside')
            ->provide_room(['workshop','workshop_lv1'])
            ->name('Seelenschmiede')
    )

    ->pop_stack()
    ;