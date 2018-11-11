<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('stables')
            ->requires_room('free')
            ->requires_room_tag('inside')
            ->name('Stall')
            ->emplaces(Model_Items_Virtual_Location_Room_Bedroom::cls())
            ->description('Naja, immerhin kann man hier den Sohn Gottes zur Welt bringen...')
            ->energy(5)
    )

    ->pop_stack()
    ;