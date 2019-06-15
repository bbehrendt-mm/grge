<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('lf_dump')
            ->requires_room('invalid')
            ->requires_room_tag('outside')
            ->clear_previous_room(true)
            ->name('Müllpresse')
            ->emplaces(Model_Items_Virtual_Location_Landfill::cls())
    )

    ->pop_stack()
    ;