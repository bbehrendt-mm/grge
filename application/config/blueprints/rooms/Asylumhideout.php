<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    ->add_blueprints(Model_Blueprint::factory()->requires_room('invalid')->room('cursed')->name('Verfluchter Raum'), true)

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('kitchen_cursed')
            ->requires_room('kitchen', 'cursed')
            ->provide_room(['kitchen','kitchen_lv1'])
            ->name('Schaurige Küche')
            ->description('Die hier herumliegenden Küchenutensilien lassen dir einen kalten Schauer über den Rücken laufen...')
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('bedroom_cursed')
            ->requires_room('bedroom', 'cursed')
            ->provide_room(['bedroom'])
            ->name('Schauriges Schlafzimmer')
            ->description('Natürlich gibt es hier ein gut ausgestattetes Schlafzimmer...')
    )

    ->pop_stack()
    ;