<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    ->add_blueprints(Model_Blueprint::factory()->id('ktc_burgerjoint')->name('Fastfood-Küche'), true)

    // ++ STACK -> All blueprints below can be produced indefinitely and require local facilities
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires_room('kitchen_burgerjoint')->category(
        'Fastfood-Küche'
    );})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:frybody')
            ->message('Alles wird leckerer, wenn man es frittiert - das wusste schon Homer Simpson. Jetzt gilt es herauszufinden, ob das auch mit Leichen funktioniert...')
            ->energy(15)
            ->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
            ->produces([Model_Items_Bodycrisp::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:coffeejnt')
            ->message('Erschöpft vom Zombieapokalypse-Alltag setzt du dir erstmal eine schöne Kanne Kaffee auf.')
            ->material([Model_Items_Coffee::cls() => 1, Model_Items_Generic_Supercharger::cls() => 1])
            ->produces([Model_Items_Coffee2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:waterjnt')
            ->message('Das Wasser in deiner Flasche schaut dich mit großen, traurigen Augen an - aber das hilft nicht viel. Eiskalt kochst du es auf 100° und tötest so alles Leben darin ab!')
            ->material([Model_Items_Generic_Waterv::cls() => 1, Model_Items_Generic_Supercharger::cls() => 1])
            ->produces([Model_Items_Generic_Water0::cls() => 1])
    )

    ->pop_stack()
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires_room('cooler')->category(
        'Kühlkammer'
    );})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:frz_body1')
            ->material([Model_Items_Body::cls() => 1])
            ->produces([Model_Items_BodyF::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:frz_body2')
            ->material([Model_Items_Body2::cls() => 1])
            ->produces([Model_Items_BodyF2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:frz_body3')
            ->material([Model_Items_Body3::cls() => 1])
            ->produces([Model_Items_BodyF3::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:frz_snow')
            ->material([Model_Items_Generic_Waterb::cls() => 1])
            ->produces([Model_Items_Snowball::cls() => 10])
    )

    ->pop_stack();
