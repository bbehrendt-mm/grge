<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

	// ++ STACK -> All blueprints below can be produced indefinitely and require local facilities
	->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires_room('workshop_weapons_1');})

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wmachete')
			->message('Deine Machete sieht schon ziemlich stumpf und rostig aus, also bringst du sie mit diesem praktischen Schleifstein wieder auf Vordermann.')
			->energy(25)
			->material([Model_Items_Machete::cls() => 1])
			->produces([Model_Items_Machete2::cls() => 1])
	)

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wammo')
			->message('Anscheinend kann man sich Munition ganz einfach selber bauen, indem man ein Kupferrohr in kleine Stücke sägt und mit Schwarzpulver füllt. Wer hätte das gedacht?')
			->material([Model_Items_Generic_Tube::cls() => 1, Model_Items_Generic_Gunpowder::cls() => 1])
			->produces([Model_Items_Ammo::cls() => 1])
	)

	// ++ STACK -> All blueprints below require more local facilities
	->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires_room('workshop_weapons_2');})

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wmachete2')
			->message('Du nimmst die Bandage, wickelst die um den Griff der Machete und befestigst die Enden mit ein wenig Klebeband. Toll, nun kannst du deine Machete viel einfacher halten und es besteht keine Gefahr mehr, beim Metzeln (oder bei der Intimrasur) abzurutschen.')
			->energy(15)
			->material([Model_Items_Machete2::cls() => 1, Model_Items_Bandage::cls() => 1, Model_Items_Generic_Ducttape::cls() => 2])
			->produces([Model_Items_Macheteband::cls() => 1])
	)

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wmachete3')
			->message('Wenn du schon ganz allein gegen Zombies kämpfen musst, kannst du dabei wenigstens cool aussehen. Leider ist niemand mehr da, um ein Video von deiner coolen neuen Choreographie bei YouTube zu posten.')
			->energy(5)
			->material([Model_Items_Machete2::cls() => 2])
			->produces([Model_Items_Macheteduo::cls() => 1])
	)

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wplasma')
			->message('Gut, dass dieser hochgradig experimentelle Plasmablitz-Generator eine absolut standartkonforme Bauform sowie Anschlüsse besitzt, sodass du ihn mit Bauteilen deines Batteriewerfers und Revolvers in eine tödliche Waffe verwandeln kannst. Fast könnte man meinen, das ganze wäre ziemlich unrealistisch.... aber nur fast!')
			->energy(60)
			->material([Model_Items_Generic_Plasma::cls() => 1, Model_Items_Generic_Electro3::cls() => 1, Model_Items_Handgun::cls() => 1, Model_Items_Batgun::cls() => 1])
			->produces([Model_Items_Plasmagun::cls() => 1])
	)

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wbatgun1')
			->message('Viel hilft viel - mit ein paar zusätzlichen Druckreglern kannst du Batterien nun mit extra-hoher Geschwindigkeit abfeuern.')
			->energy(5)
			->material([Model_Items_Generic_Pressure::cls() => 3, Model_Items_Generic_Sum::cls() => 1, Model_Items_Batgun2::cls() => 1])
			->produces([Model_Items_Batgunsplat::cls() => 1])
	)

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wbatgun2')
			->message('Verlängerter Lauf: Check. Feinjustierte Abschussvorrichtung: Check. Rosa glitzernder Ponyaufkleber: Check. Tötungsmaschine: Bereit!')
			->energy(5)
			->material([Model_Items_Generic_Tube::cls() => 5, Model_Items_Generic_Sum::cls() => 2, Model_Items_Batgun2::cls() => 1])
			->produces([Model_Items_Batgunsnp::cls() => 1])
	)

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:wsnowgun')
            ->message('Warum sollte man nur Batterien abschießen können?')
            ->energy(5)
            ->material([Model_Items_Generic_Pressure::cls() => 1, Model_Items_Generic_Cooler::cls() => 1, Model_Items_Batgun::cls() => 1, Model_Items_Generic_Tube::cls() => 1])
            ->produces([Model_Items_Snowgun::cls() => 1])
    )

	->drop_stack();