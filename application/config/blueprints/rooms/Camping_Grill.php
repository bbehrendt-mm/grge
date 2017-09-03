<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('bbq_grill')
            ->requires_room('invalid')
            ->requires_room_tag('outside')
            ->name('Grillstation')
            ->emplaces_action("Grillen ...")
    )

    ->pop_stack()
    ;