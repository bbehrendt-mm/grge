<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // External stuff
    ->add_blueprints(Model_Blueprint::factory()->id('hideout_slot')->name('Sicheres Versteck'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('outside')->name('Bebaubarer Aussenbereich'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('outside_space')->name('Großflächiger Aussenbereich'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('impaler')->name('Vorbereitete Fallgruben'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('slot_epic')->name('Bauplatz für epische Projekte'), true)

    ->add_blueprints(Model_Blueprint::factory()->room('free')->name('Unbenutzter Raum'), true)

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier(Model_Blueprint::BP_MOD_ENERGY, function($pl,$pre,$e) { /** @var Model_Player $pl */
            $mod = 1;
            if (Tool_Scripts::get_timeofday($pl) == 'morning') $mod -= 0.25;    // Daytime bonus
            if ($pl->get_status()->retrieve('tr_handyman')) $mod -= 0.1;                     // Handyman Bonus
            return max(min(1,$e),floor($e*$mod));
        });
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('kitchen')
            ->requires_room('free')
            ->name('Küche')
            ->description('In einer Küche kannst du aus diversen Gegenständen Nahrungsmittel zubereiten. Für deren Geschmack wird jedoch keine Garantie übernommen...')
            ->energy(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('bedroom')
            ->requires_room('free')
            ->name('Schlafzimmer')
            ->description('Das Schlafzimmer bietet dir die Möglichkeit, nach einem harten Tag in der Postapokalypse endlich etwas Ruhe und Frieden zu finden.')
            ->energy(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('community')
            ->requires_room('free')
            ->name('Gemeinschaftsraum')
            ->description('In diesem Raum kannst du deine Zeit verbringen, wenn du mit Schlafen, Zombies töten sowie deiner Steuererklärung fertig bist.')
            ->energy(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('workshop')
            ->requires_room('free')
            ->name('Werkstatt')
            ->description('Die Werkstatt kann mit vielerlei Werkzeugen und Geräten ausgestattet werden, die für alltägliche Bastelarbeiten erforderlich sind.')
            ->energy(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('utilities')
            ->requires_room('free')
            ->name('Wirtschaftsraum')
            ->description('Hier kannst du alle möglichen großen Geräte unterbringen, die deinem Versteck die Annehmlichkeiten einer luxoriösen 5-Sterne-Bruchbude verleihen.')
            ->energy(5)
    )

    //++ STACK -> EPIC FOUNDATIONS
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->energy(15)->message('Du hast die Arbeiten an einem epischen Projekt in deinem Versteck begonnen. Viel Erfolg!')->effect(Model_Effect::factory()->achieve(Model_Achievement::MA_EPIC_BEGIN, 1, true));})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('epc_garden')
            ->requires_room('free')
            ->name('Kleines Gewächshaus')
            ->description('Wie Millionen von Pot-Farmern vor dir kannst auch du mit diesem patentierten Gewächshaus-Bausatz deinen grünen Daumen entdecken und verschiedene nützliche Gewächse anpflanzen. Aber Achtung: Ein solcher Garten benötigt viel Aufmerksamkeit und Zeit, bevor du etwas ernten kannst!')
    )

    /*->add_blueprints(
        Model_Blueprint::factory()
            ->room('epc_drill')
            ->name('Grundwasserversorgung')
            ->description('Warum gammliges Kondenswasser von alten Bahnhofstoiletten ablecken, wenn du dir frisches Wasser aus dem Boden besorgen kannst? Zwar wird der Bau dieses Projektes dich sehr viel Energie kosten, dafür verfügst du danach über eine (zumindest halbwegs) stetige Wasserversorgung.')
    )*/

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('epc_raven')
            ->requires_room('free')
            ->name('Raben-Bootcamp')
            ->description('Raben sind intelligente (und boshafte) Tiere - aber mit ein bisschen Geschick könntest du sie vielleicht dazu trainieren, für dich nach Gegenständen zu suchen. Du müsstest sie dafür natürlich mit etwas Futter belohnen...')
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('epc_fence')
            ->requires_room('free')
            ->name('Laserzaun')
            ->description('Zombies sind nicht gerade für ihre Geschicklichkeit bekannt - daher kannst du sie mit ein paar Laserbarrieren bestimmt recht zuverlässig von deinem Versteck fernhalten. Vorrausgesetzt natürlich, dir gehen nicht die Batterien aus...')
    )

    // -- STACK -> Categories
    ->pop_stack()



    ->pop_stack()
    ->validate();