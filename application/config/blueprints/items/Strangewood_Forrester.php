<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    ->add_blueprints(Model_Blueprint::factory()->id('ktc_burgerjoint')->name('Fastfood-Küche'), true)

    // ++ STACK -> All blueprints below can be produced indefinitely and require local facilities
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires_room('workshop_hw19_forrester')->category(
        'Seelenschmiede'
    );})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:h19_helmet1')
            ->energy(10)
            ->material([Model_Items_Pumpkin::cls() => 1])
            ->produces([Model_Items_Halloween_Helmet::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:h19_helmet2')
            ->energy(15)
            ->material([Model_Items_Halloween_Helmet::cls() => 1, Model_Items_Soul::cls() => 3])
            ->produces([Model_Items_Halloween_Helmet2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:h19_helmet3')
            ->energy(15)
            ->material([Model_Items_Halloween_Helmet::cls() => 1, Model_Items_Soul2::cls() => 1])
            ->produces([Model_Items_Halloween_Helmet3::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:h19_jacket1')
            ->energy(15)
            ->material([Model_Items_Pumpkin::cls() => 2])
            ->produces([Model_Items_Halloween_Jacket::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:h19_jacket2')
            ->energy(20)
            ->material([Model_Items_Halloween_Jacket::cls() => 1, Model_Items_Soul::cls() => 5])
            ->produces([Model_Items_Halloween_Jacket2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:h19_jacket3')
            ->energy(20)
            ->material([Model_Items_Halloween_Jacket::cls() => 1, Model_Items_Soul2::cls() => 2])
            ->produces([Model_Items_Halloween_Jacket3::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:h19_aug1')
            ->energy(25)
            ->material([Model_Items_Organ::cls() => 1, Model_Items_Soul::cls() => 4])
            ->produces([Model_Items_Halloween_Augment::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:h19_aug2')
            ->energy(25)
            ->material([Model_Items_Organ::cls() => 1, Model_Items_Soul2::cls() => 2])
            ->produces([Model_Items_Halloween_Augment2::cls() => 1])
    )

    ->pop_stack();
