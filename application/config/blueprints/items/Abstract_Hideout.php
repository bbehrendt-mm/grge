<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below can be produced indefinitely
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0);})

    // ++ STACK -> All blueprints below need the hideout
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('hideout_slot');})

    // ++ STACK -> All blueprints below need the basic generator
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('utilities')->category('Generator');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:genbat1')
            ->requires('gen1')
            ->name('1 mAh aus Batterien erzeugen')
            ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
            ->material([Model_Items_Battery::cls() => 2])
            ->produces([Model_Items_Energy::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:genbat2')
            ->requires('gen1')
            ->name('5 mAh aus Batterien erzeugen')
            ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
            ->material([Model_Items_Battery::cls() => 10])
            ->produces([Model_Items_Energy::cls() => 5])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:genbat3')
            ->requires('gen1')
            ->name('2 mAh aus Supercharger erzeugen')
            ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
            ->material([Model_Items_Generic_Supercharger::cls() => 1])
            ->produces([Model_Items_Energy::cls() => 2])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:genbat4')
            ->requires('gen1')
            ->message('Wer glaubt schon an Herstellerangaben! Diese Batterie kann problemlos so stark aufgeladen werden, dass man damit eine Kleinstadt mehrere Tage mit Strom versorgen könnte. Und diese Gerüchte von wegen "Explosionsgefahr" sind bestimmt bloß Panikmache aus den Medien ...')
            ->material([Model_Items_Battery::cls() => 1, Model_Items_Energy::cls() => 2])
            ->produces([Model_Items_Generic_Supercharger::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:gengas1')
            ->requires('gen2')
            ->name('10 mAh aus Benzin erzeugen')
            ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
            ->material([Model_Items_Generic_Jerrycan::cls() => 1])
            ->produces([Model_Items_Energy::cls() => 10])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:genmanual1')
            ->requires('gen2')
            ->name('1 mAh mit Muskelkraft erzeugen')
            ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
            ->energy(45)
            ->produces([Model_Items_Energy::cls() => 1])
    )

    // -- STACK -> All blueprints below NO LONGER need the basic generator
    ->pop_stack()

    ->drop_stack();