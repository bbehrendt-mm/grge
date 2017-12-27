<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below can be produced indefinitely and require local facilities
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires_room('manu_northpole');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:xmas1')
            ->message('Nichts macht mehr Spaß als an Weihnachten etwas schönes zu basteln. Du hast soeben Dekoration hergestellt.')
            ->energy(5)
            ->material([Model_Items_Generic_Cd::cls() => 3])
            ->produces([Model_Items_Generic_Lametta::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:xmas2')
            ->message('Nichts macht mehr Spaß als an Weihnachten etwas schönes zu basteln. Du hast soeben Dekoration hergestellt.')
            ->energy(5)
            ->material([Model_Items_Generic_Lametta::cls() => 2, Model_Items_Generic_Wire::cls() => 1])
            ->produces([Model_Items_Generic_Xmasrope::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:xmas3')
            ->message('Nichts macht mehr Spaß als an Weihnachten etwas schönes zu basteln. Du hast soeben Dekoration hergestellt.')
            ->energy(5)
            ->material([Model_Items_Generic_Electro::cls() => 1, Model_Items_Generic_Wire::cls() => 1, Model_Items_Generic_Led::cls() => 10])
            ->produces([Model_Items_Generic_Xmaslights::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:xmas4')
            ->message('Nichts macht mehr Spaß als an Weihnachten etwas schönes zu basteln. Du hast soeben Dekoration hergestellt.')
            ->energy(5)
            ->material([Model_Items_Generic_Bauble::cls() => 3, Model_Items_Stick::cls() => 2, Model_Items_Generic_Ducttape::cls() => 1, Model_Items_Generic_Xmasneedles::cls() => 1])
            ->produces([Model_Items_Generic_Mistletoe::cls() => 1])
    )

    ->drop_stack();