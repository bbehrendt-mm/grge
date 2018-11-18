<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('kitchen_burgerjoint')
            ->requires_room('invalid')
            ->requires_room_tag('inside')
            ->provide_room(['kitchen','kitchen_lv1'])
            ->name('Fastfood-Küche')
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('cooler_closed')
            ->requires_room('invalid')
            ->requires_room_tag('inside')
            ->emplaces(Model_Items_Virtual_Location_Room_Cooler::cls())
            ->name('Kühlkammer (verschlossen)')
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('cooler')
            ->requires_room('invalid')
            ->requires_room_tag('inside')
            ->emplaces_action('Einfrieren...')
            ->name('Kühlkammer')
    )

    ->pop_stack()
    ;