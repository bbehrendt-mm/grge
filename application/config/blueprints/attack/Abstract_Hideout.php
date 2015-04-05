<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below can be produced indefinitely
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0);})


    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('z:impale')
            ->requires('impaler')
            ->name('Falltür öffnen')
            ->energy(2)
            ->zombies(false, function($z) {
                return floor($z/2);
            })
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('z:batg1')
            ->requires('defbat')
            ->name('Eine Batterie abfeuern')
            ->material(['Model_Items_Battery' => 1])
            ->zombies(false, 1)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('z:batg2')
            ->requires('defbat')
            ->name('Zwei Batterien abfeuern')
            ->material(['Model_Items_Battery' => 2])
            ->zombies(false, 2)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('z:batg4')
            ->requires('defbat')
            ->name('Fünf Batterien abfeuern')
            ->material(['Model_Items_Battery' => 5])
            ->zombies(false, 5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('z:batg3')
            ->requires('defbat')
            ->name('Supercharger abfeuern')
            ->material(['Model_Items_Generic_Supercharger' => 1])
            ->zombies(false, function($z) {
                return [
                    5,
                    5 + floor($z/3)
                ];
            })
    )

    ->drop_stack();