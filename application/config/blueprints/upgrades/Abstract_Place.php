<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // External stuff
    ->add_blueprints(Model_Blueprint::factory()->id('hideout_slot')->name('Sicheres Versteck'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('impaler')->name('Vorbereitete Fallgruben'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('slot_epic')->name('Bauplatz für epische Projekte'), true)

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    // Kitchen
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Küche')->requires_room('kitchen');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc2')
            ->name('Wasserkocher')
            ->description('Schaltet zusätzliche Optionen in der Küche frei.')
            ->message('Das zentrale Utensil jeder Küche - der Wasserkocher - steht nun auch dir zur Verfügung. Nutze ihn Weise, und missbrauche seine Kräfte nicht!')
            ->energy(2)
            ->material([Model_Items_Generic_Boiler::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc3')
            ->name('Küchenutensilien')
            ->description('Schaltet zusätzliche Optionen in der Küche frei.')
            ->message('ENDLICH! Nun musst du den Brei nicht mehr mit der Hand kneten und das Fleisch nicht mehr mit Karateschlägen schneiden. Heureka!')
            ->material([Model_Items_Generic_Mixer::cls() => 1, Model_Items_Knife::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc4')
            ->name('Ofen')
            ->description('Schaltet zusätzliche Optionen in der Küche frei.')
            ->message('Die Zeiten von eiskaltem Essen sind vorbei! Vorrausgesetzt natürlich, du kannst etwas Strom auftreiben ...')
            ->energy(20)
            ->material([Model_Items_Generic_Oven::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc_cool')
            ->name('Kühlschrank')
            ->description('Schaltet zusätzliche Optionen in der Küche frei.')
            ->message('Das zentrale Utensil jeder Küche - der Wasserkocher - steht nun auch dir zur Verfügung. Nutze ihn Weise, und missbrauche seine Kräfte nicht!')
            ->energy(10)
            ->material([Model_Items_Generic_Cooler::cls() => 1, Model_Items_Generic_Sum::cls() => 2, Model_Items_Generic_Tube::cls() => 2, Model_Items_Generic_Electro::cls() => 1, Model_Items_Generic_Metal::cls() => 2])
    )

    // -- STACK -> All blueprints below NO LONGER need the kitchen
    ->pop_stack()

    // Workbench
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('workshop');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('manuspd')
            ->name('Werkbank-Halterungen')
            ->description('Reduziert die benötigte Energie für alle Arbeiten an der Werkbank.')
            ->message('Diese neuen Halterungen werden sich sicher als nützlich erweisen, wenn es mal etwas schweres zu heben gibt. Hoffentlich hast du beim bau nicht gepfuscht, sonst werden sie sich zusätzlich noch als tödlich erweisen...')
            ->energy(15)
            ->material([Model_Items_Generic_Wood::cls() => 3, Model_Items_Generic_Sum::cls() => 1])
    )

    // -- STACK -> All blueprints below NO LONGER need the workbench
    ->pop_stack()

    ->pop_stack()
    ;