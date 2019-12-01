<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('rudolph_upgrader')
            ->requires_room('invalid','free')
            ->requires_room_tag('inside')
            ->name('Cringles Operationssaal')
            ->emplaces_action('An Rudolph experimentieren...')
    )

    ->pop_stack()
    ;