<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Schlafzimmer')->requires_room('stables');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hay1')
            ->name('Heu')
            ->description('Immerhin besser, als auf dem Boden zu schlafen...')
            ->category('Schlafzimmer')
    )


    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hay2')
            ->requires_local('hay1')
            ->name('Heu mit Tannennadeln')
            ->description('Verbessert Regeneration von Energie und Müdigkeit beim Schlafen.')
            ->message('Jetzt bekommst du mit jedem mal Schlafen direkt noch eine Akupunkturbehandlung! Was will man mehr?')
            ->deco(1)
            ->energy(5)
            ->material(['Model_Items_Generic_Xmasneedles' => 5])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hay3')
            ->requires_local('hay2')
            ->name('Heu mit Lametta')
            ->description('Verbessert Regeneration von Energie und Müdigkeit beim Schlafen.')
            ->message('Dieses Lametta findet den Weg IN - JEDE - VERDAMMTE - RITZE!!!')
            ->deco(5)
            ->energy(5)
            ->material(['Model_Items_Generic_Lametta' => 5])
    )

    ->pop_stack()

    ->pop_stack()
    ;