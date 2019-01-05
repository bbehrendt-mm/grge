<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('bedroom')
            ->requires_room('free')
            ->requires_room_tag('inside')
            ->name('Schlafzimmer')
            ->emplaces(Model_Items_Virtual_Location_Room_Bedroom::cls())
            ->description('Das Schlafzimmer bietet dir die Möglichkeit, nach einem harten Tag in der Postapokalypse endlich etwas Ruhe und Frieden zu finden.')
            ->energy(5)
            ->space(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('community')
            ->requires_room('free')
            ->name('Gemeinschaftsraum')
            ->emplaces(Model_Items_Virtual_Location_Room_Community::cls())
            ->description('In diesem Raum kannst du deine Zeit verbringen, wenn du mit Schlafen, Zombies töten sowie deiner Steuererklärung fertig bist.')
            ->energy(5)
            ->space(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('utilities')
            ->requires_room('free')
            ->requires_room_tag('inside')
            ->name('Wirtschaftsraum')
            ->emplaces_action()
            ->description('Hier kannst du alle möglichen großen Geräte unterbringen, die deinem Versteck die Annehmlichkeiten einer luxoriösen 5-Sterne-Bruchbude verleihen.')
            ->energy(5)
            ->space(1)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('outside_defense')
            ->global_blocking(false)
            ->requires_room('free')
            ->requires_room_tag('outside')
            ->name('Verteidigungslinie')
            ->description('Wie uns Plants Vs. Zombies gelehrt hat, lässt sich ein Petuniengarten wunderbar für die Verteidigung des eigenen Verstecks nutzen.')
            ->energy(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('radiotower')
            ->requires_room('invalid')
            ->requires_room_tag('inside')
            ->clear_previous_room(true)
            ->emplaces(Model_Items_Virtual_Location_Mapmode::cls())
            ->name('Funkraum')
    )

    //++ STACK -> EPIC FOUNDATIONS
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->energy(15)->message('Du hast die Arbeiten an einem epischen Projekt in deinem Versteck begonnen. Viel Erfolg!')->effect(Model_Effect::factory()->achieve(Model_Achievement::MA_EPIC_BEGIN, 1, true));})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('epc_garden')
            ->requires_room('free')
            ->requires_room_tag('inside')
            ->name('Kleines Gewächshaus')
            ->description('Wie Millionen von Pot-Farmern vor dir kannst auch du mit diesem patentierten Gewächshaus-Bausatz deinen grünen Daumen entdecken und verschiedene nützliche Gewächse anpflanzen. Aber Achtung: Ein solcher Garten benötigt viel Aufmerksamkeit und Zeit, bevor du etwas ernten kannst!')
            ->space(20)
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
            ->requires_room_tag('inside')
            ->name('Raben-Bootcamp')
            ->description('Raben sind intelligente (und boshafte) Tiere - aber mit ein bisschen Geschick könntest du sie vielleicht dazu trainieren, für dich nach Gegenständen zu suchen. Du müsstest sie dafür natürlich mit etwas Futter belohnen...')
            ->space(20)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('epc_fence')
            ->requires_room('free')
            ->requires_room_tag('outside')
            ->name('Laserzaun')
            ->description('Zombies sind nicht gerade für ihre Geschicklichkeit bekannt - daher kannst du sie mit ein paar Laserbarrieren bestimmt recht zuverlässig von deinem Versteck fernhalten. Vorrausgesetzt natürlich, dir gehen nicht die Batterien aus...')
            ->space(20)
    )

    // -- STACK -> Categories
    ->pop_stack()



    ->pop_stack()
    ;