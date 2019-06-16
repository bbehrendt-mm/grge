<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

	// ++ STACK -> All blueprints below can be produced indefinitely and require local facilities
	->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires_room('workshop_garage');})

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:gsum1')
			//->message('Deine Machete sieht schon ziemlich stumpf und rostig aus, also bringst du sie mit diesem praktischen Schleifstein wieder auf Vordermann.')
			->energy(15)
			->material([Model_Items_Generic_Metal::cls() => 3])
			->produces([Model_Items_Generic_Sum::cls() => 2])
	)

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:gsum2')
            //->message('Deine Machete sieht schon ziemlich stumpf und rostig aus, also bringst du sie mit diesem praktischen Schleifstein wieder auf Vordermann.')
            ->energy(45)
            ->material([Model_Items_Generic_Crmetal::cls() => 3])
            ->produces([Model_Items_Generic_Sum::cls() => 2])
    )

	->drop_stack();