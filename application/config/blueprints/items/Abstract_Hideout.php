<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below can be produced indefinitely
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0);})

    // ++ STACK -> All blueprints below need the hideout
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('hideout');})

    // ++ STACK -> All blueprints below need the basic kitchen
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('ktc1');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:soft')
            ->message('Nur wenige wissen, dass sich Softdrinks in klares Wasser umwandeln lassen, indem man einfach zwei von ihnen zusammenmischt. Gut, dass du in Chemie immer so gut aufgepasst hast!')
            ->energy(1)
            ->material(['Model_Items_Softdrink' => 2])
            ->produces(['Model_Items_Generic_Water0' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:body1')
            ->requires('ktc3')
            ->name('Ausgenommene Leiche')
            ->message('Du benutzt deine Machete, um die Leiche in kleine Stücke zu schneiden. Das macht sie zwar nicht genießbarer, aber zumindest handlicher.')
            ->energy(15)
            ->material(['Model_Items_Body' => 1])
            ->produces(['Model_Items_Rawmeat' => 8, 'Model_Items_Generic_Waterb' => 3])
            ->effect(
                Model_Effect::factory()
                    ->achieve(Model_Achievement::MA_BLOODSUCKER)
            )
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:meat')
            ->requires('ktc4')
            ->name('Abgekochte Knochen')
            ->message('Nachdem du die Knochen mit Fleisch in den Ofen gelegt hast, ist deine Küche erfüllt von .... leckerem .... Geruch. Aber wenigstens kannst du die Knochen nun essen, ohne dir eine Vergiftung zuzuziehen.')
            ->energy(10)
            ->material(['Model_Items_Rawmeat' => 4, 'Model_Items_Energy' => 2])
            ->produces(['Model_Items_Rawmeat2' => 4])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:coffee')
            ->requires('ktc2')
            ->message('Erschöpft vom Zombieapokalypse-Alltag setzt du dir erstmal eine schöne Kanne Kaffee auf.')
            ->material(['Model_Items_Coffee' => 1, 'Model_Items_Energy' => 2])
            ->produces(['Model_Items_Coffee2' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:water')
            ->requires('ktc2')
            ->message('Das Wasser in deiner Flasche schaut dich mit großen, traurigen Augen an - aber das hilft nicht viel. Eiskalt kochst du es auf 100° und tötest so alles Leben darin ab!')
            ->material(['Model_Items_Generic_Waterv' => 1, 'Model_Items_Energy' => 2])
            ->produces(['Model_Items_Generic_Water0' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:nom1')
            ->requires('ktc3')
            ->message('Wenn man zwei Grundnahrungsmittel kombiniert, kann man unter umständen ein ganz neues Nahrungsmittel erschaffen. Diesen Vorgang nennt man "kochen", und du hast ihn soeben erfolgreich durchgeführt.')
            ->material(['Model_Items_Basefood' => 2])
            ->energy(10)
            ->produces(['Model_Items_Nom' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:nom2')
            ->requires('ktc4')
            ->message('Eine leckere Speise ist nur halb so gut, wenn sie kalt und ungewürzt ist. Du streust also ein paar Gewürze drüber und lässt das ganze eine Weile im Ofen schmoren - voilá, du hast deine Speise noch leckerer gemacht!')
            ->material(['Model_Items_Nom' => 1, 'Model_Items_Generic_Spice' => 1])
            ->energy(10)
            ->produces(['Model_Items_Nom2' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:pksoup')
            ->message('Zerstampfen, verrühren, würzen. Für dieses Rezept muss man kein Meisterkoch sein, und man kann es in allen Lebenslagen anwenden (Kürbisse zubereiten, Gespräche mit dem Finanzamt etc) ')
            ->material(['Model_Items_Pumpkin' => 1, 'Model_Items_Generic_Spice' => 1, 'Model_Items_Generic_Waterv' => 1])
            ->energy(10)
            ->produces(['Model_Items_Pumpkinsoup' => 5])
    )

    // -- STACK -> All blueprints below NO LONGER need the basic kitchen
    ->pop_stack()

    // ++ STACK -> All blueprints below need the basic generator
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('gen1');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:genbat1')
            ->name('1 mAh aus Batterien erzeugen')
            ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
            ->material(['Model_Items_Battery' => 2])
            ->produces(['Model_Items_Energy' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:genbat2')
            ->name('5 mAh aus Batterien erzeugen')
            ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
            ->material(['Model_Items_Battery' => 10])
            ->produces(['Model_Items_Energy' => 5])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:genbat3')
            ->name('2 mAh aus Supercharger erzeugen')
            ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
            ->material(['Model_Items_Generic_Supercharger' => 1])
            ->produces(['Model_Items_Energy' => 2])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:genbat4')
            ->message('Wer glaubt schon an Herstellerangaben! Diese Batterie kann problemlos so stark aufgeladen werden, dass man damit eine Kleinstadt mehrere Tage mit Strom versorgen könnte. Und diese Gerüchte von wegen "Explosionsgefahr" sind bestimmt bloß Panikmache aus den Medien ...')
            ->material(['Model_Items_Battery' => 1, 'Model_Items_Energy' => 2])
            ->produces(['Model_Items_Generic_Supercharger' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:gengas1')
            ->requires('gen2')
            ->name('10 mAh aus Benzin erzeugen')
            ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
            ->material(['Model_Items_Generic_Jerrycan' => 1])
            ->produces(['Model_Items_Energy' => 10])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:genmanual1')
            ->requires('gen2')
            ->name('1 mAh mit Muskelkraft erzeugen')
            ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
            ->energy(45)
            ->produces(['Model_Items_Energy' => 1])
    )

    // -- STACK -> All blueprints below NO LONGER need the basic generator
    ->pop_stack()

    // ++ STACK -> All blueprints below need the basic workbench
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('manu1');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:bike')
            ->message('Das war einfacher als du dachtest - dein Fahrrad ist nun wieder einsatzbereit!')
            ->energy(15)
            ->material(['Model_Items_Generic_Bike' => 1, 'Model_Items_Generic_Sum' => 2, 'Model_Items_Generic_Belt' => 1])
            ->produces(['Model_Items_Generic_Bike2' => 1])
    )

    ->drop_stack();