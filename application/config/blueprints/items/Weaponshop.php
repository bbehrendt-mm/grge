<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
	->add_blueprints(Model_Blueprint::factory()->id('manu_wpn1')->name('Kleine Waffen-Reparaturwerkstatt'), true)
	->add_blueprints(Model_Blueprint::factory()->id('manu_wpn2')->name('Umfrangreiche Waffen-Reparaturwerkstatt'), true)

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->add_modifier(Model_Blueprint::BP_MOD_ENERGY, function($pl,$pre,$e) { /** @var Model_Player $pl */
        $mod = 1;
        if (Tool_Scripts::get_timeofday($pl) == 'morning') $mod -= 0.25;    // Daytime bonus
        if ($pl->buff_retr('tr_handyman')) $mod -= 0.1;                     // Handyman Bonus

        return max(min(1,$e),floor($e*$mod));
    });})

	// ++ STACK -> All blueprints below can be produced indefinitely and require local facilities
	->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires('manu_wpn1');})

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wmachete')
			->message('Deine Machete sieht schon ziemlich stumpf und rostig aus, also bringst du sie mit diesem praktischen Schleifstein wieder auf Vordermann.')
			->energy(25)
			->material(['Model_Items_Machete' => 1])
			->produces(['Model_Items_Machete2' => 1])
	)

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wammo')
			->message('Anscheinend kann man sich Munition ganz einfach selber bauen, indem man ein Kupferrohr in kleine Stücke sägt und mit Schwarzpulver füllt. Wer hätte das gedacht?')
			->material(['Model_Items_Generic_Tube' => 1, 'Model_Items_Generic_Gunpowder' => 1])
			->produces(['Model_Items_Ammo' => 1])
	)

	// ++ STACK -> All blueprints below require more local facilities
	->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires('manu_wpn2');})

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wmachete2')
			->message('Du nimmst die Bandage, wickelst die um den Griff der Machete und befestigst die Enden mit ein wenig Klebeband. Toll, nun kannst du deine Machete viel einfacher halten und es besteht keine Gefahr mehr, beim Metzeln (oder bei der Intimrasur) abzurutschen.')
			->energy(15)
			->material(['Model_Items_Machete2' => 1, 'Model_Items_Bandage' => 1, 'Model_Items_Generic_Ducttape' => 2])
			->produces(['Model_Items_Macheteband' => 1])
	)

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wmachete3')
			->message('Wenn du schon ganz allein gegen Zombies kämpfen musst, kannst du dabei wenigstens cool aussehen. Leider ist niemand mehr da, um ein Video von deiner coolen neuen Choreographie bei YouTube zu posten.')
			->energy(5)
			->material(['Model_Items_Machete2' => 2])
			->produces(['Model_Items_Macheteduo' => 1])
	)

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wplasma')
			->message('Gut, dass dieser hochgradig experimentelle Plasmablitz-Generator eine absolut standartkonforme Bauform sowie Anschlüsse besitzt, sodass du ihn mit Bauteilen deines Batteriewerfers und Revolvers in eine tödliche Waffe verwandeln kannst. Fast könnte man meinen, das ganze wäre ziemlich unrealistisch.... aber nur fast!')
			->energy(60)
			->material(['Model_Items_Generic_Plasma' => 1, 'Model_Items_Generic_Electro' => 1, 'Model_Items_Handgun' => 1, 'Model_Items_Batgun' => 1])
			->produces(['Model_Items_Plasmagun' => 1])
	)

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wbatgun1')
			->message('Viel hilft viel - mit ein paar zusätzlichen Druckreglern kannst du Batterien nun mit extra-hoher Geschwindigkeit abfeuern.')
			->energy(5)
			->material(['Model_Items_Generic_Pressure' => 3, 'Model_Items_Generic_Sum' => 1, 'Model_Items_Batgun2' => 1])
			->produces(['Model_Items_Batgunsplat' => 1])
	)

	->add_blueprints(
		Model_Blueprint::factory()
			->id('i:wbatgun2')
			->message('Verlängerter Lauf: Check. Feinjustierte Abschussvorrichtung: Check. Rosa glitzernder Ponyaufkleber: Check. Tötungsmaschine: Bereit!')
			->energy(5)
			->material(['Model_Items_Generic_Tube' => 5, 'Model_Items_Generic_Sum' => 2, 'Model_Items_Batgun2' => 1])
			->produces(['Model_Items_Batgunsnp' => 1])
	)

	->drop_stack();