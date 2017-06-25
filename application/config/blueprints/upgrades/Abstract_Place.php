<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // External stuff
    ->add_blueprints(Model_Blueprint::factory()->id('hideout_slot')->name('Sicheres Versteck'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('outside')->name('Bebaubarer Aussenbereich'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('outside_space')->name('Großflächiger Aussenbereich'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('impaler')->name('Vorbereitete Fallgruben'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('slot_epic')->name('Bauplatz für epische Projekte'), true)

    // Kitchen
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Küche')->requires_room('kitchen');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc2')
            ->name('Wasserkocher')
            ->description('Schaltet zusätzliche Optionen in der Küche frei.')
            ->message('Das zentrale Utensil jeder Küche - der Wasserkocher - steht nun auch dir zur Verfügung. Nutze ihn Weise, und missbrauche seine Kräfte nicht!')
            ->energy(2)
            ->material(['Model_Items_Generic_Boiler' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc3')
            ->name('Küchenutensilien')
            ->description('Schaltet zusätzliche Optionen in der Küche frei.')
            ->message('ENDLICH! Nun musst du den Brei nicht mehr mit der Hand kneten und das Fleisch nicht mehr mit Karateschlägen schneiden. Heureka!')
            ->material(['Model_Items_Generic_Mixer' => 1, 'Model_Items_Knife' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc4')
            ->name('Ofen')
            ->description('Schaltet zusätzliche Optionen in der Küche frei.')
            ->message('Die Zeiten von eiskaltem Essen sind vorbei! Vorrausgesetzt natürlich, du kannst etwas Strom auftreiben ...')
            ->energy(20)
            ->material(['Model_Items_Generic_Oven' => 1])
    )

    // -- STACK -> All blueprints below NO LONGER need the kitchen
    ->pop_stack()

    // Workbench
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('workshop');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('manu1')
            ->name('Werkbank')
            ->description('Ermöglicht die Herstellung verschiedener Gegenstände.')
            ->message('Ein Mann ohne Werkbank ist einfach kein richtiger Mann! (Eine Frau ohne Werkbank ist natürlich auch kein richtiger Mann.) Jetzt kannst du endlich viel Geld ausgeben und Zeug bauen, dass viel weniger kosten würde wenn du es einfach fertig kaufen würdest. Hurra!')
            ->energy(10)
            ->material(['Model_Items_Generic_Table' => 1, 'Model_Items_Abstract_Chair' => 1])
    )

    // ++ STACK -> All blueprints below need the workbench
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('manu1');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('manu2')
            ->name('Stromversorgung an der Werkbank')
            ->description('Schaltet zusätzliche Optionen für die Werkbank frei.')
            ->message('Ohne das Risiko tödlicher Stromschläge macht die Arbeit einfach keinen Spaß! Darum sind offene Drähte ohne Sicherung einfach ein Muss für jede Werkbank!')
            ->energy(10)
            ->material(['Model_Items_Generic_Lamp' => 1, 'Model_Items_Generic_Electro' => 3, 'Model_Items_Energy' => 5])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('manuspd')
            ->name('Werkbank-Halterungen')
            ->description('Reduziert die benötigte Energie für alle Arbeiten an der Werkbank.')
            ->message('Diese neuen Halterungen werden sich sicher als nützlich erweisen, wenn es mal etwas schweres zu heben gibt. Hoffentlich hast du beim bau nicht gepfuscht, sonst werden sie sich zusätzlich noch als tödlich erweisen...')
            ->energy(15)
            ->material(['Model_Items_Generic_Wood' => 3, 'Model_Items_Generic_Sum' => 1])
    )

    // -- STACK -> All blueprints below NO LONGER need the workbench
    ->pop_stack()

    ;