<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('container_closed')
            ->requires_room('invalid')
            ->requires_room_tag('inside')
            ->clear_previous_room(true)
            ->emplaces(Model_Items_Virtual_Location_Room_Container::cls())
            ->name('Baucontainer (verschlossen)')
    )

    ->pop_stack()
    ;