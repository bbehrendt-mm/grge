<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('motorhome')
            ->requires_room('free')
            ->provide_room(['kitchen','workshop','bedroom','community','utilities'])
            ->name('Wohnmobil')
            ->emplaces_action()
            ->emplaces(Model_Items_Virtual_Location_Room_Community::cls())
            ->emplaces(Model_Items_Virtual_Location_Room_Bedroom::cls())
            ->energy(500)
    )
    ->pop_stack()

    ;