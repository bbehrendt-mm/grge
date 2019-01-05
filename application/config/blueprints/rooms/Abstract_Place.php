<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // External stuff
    ->add_blueprints(Model_Blueprint::factory()->id('hideout_slot')->name('Sicheres Versteck'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('impaler')->name('Vorbereitete Fallgruben'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('slot_epic')->name('Bauplatz für epische Projekte'), true)

    ->add_blueprints(Model_Blueprint::factory()->requires_room('invalid')->room('free')->name('Unbenutzter Raum'))

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    /** KITCHEN */

    ->add_blueprints(Model_Blueprint::factory()->room('kitchen_lv1')->name('Ausgebaute Küche'), true)
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('kitchen')
            ->requires_room('free')
            ->material([Model_Items_Generic_Table::cls() => 1])
            ->name('Küche')
            ->emplaces_action('Kochen')
            ->description('In einer Küche kannst du aus diversen Gegenständen Nahrungsmittel zubereiten. Für deren Geschmack wird jedoch keine Garantie übernommen...')
            ->energy(5)
            ->space(2)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('kitchen_slaughter')
            ->requires_room('kitchen')
            ->requires_room_tag('inside')
            ->replace_room_satisfaction(false)
            ->material([Model_Items_Generic_Table::cls() => 1, Model_Items_Knife::cls() => 2])
            ->provide_room(['kitchen_lv1'])
            ->name('Schlachthaus')
            ->description('Es gibt überraschend viele Dinge, die sich zwecks Nahrungsgewinnung schlachten lassen (meist jedoch unfreiwillig). Eine Vorraussetzung dafür ist natürlich der Bau eines Schlachthauses.')
            ->energy(15)
            ->space(2)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('kitchen_meth')
            ->requires_room('kitchen')
            ->requires_room_tag('inside')
            ->replace_room_satisfaction(false)
            ->material([Model_Items_Bottle::cls() => 5, Model_Items_Generic_Tube::cls() => 2])
            ->provide_room(['kitchen_lv1'])
            ->name('Drogenküche')
            ->description('Warum sollte man nur Nahrungsmittel kochen? Mit ein wenig zusätzlicher Ausrüstung kannst du auch leckere 5-Sterne-Drogen zubereiten!')
            ->energy(15)
            ->space(1)
    )

    /** WORKSHOP */

    ->add_blueprints(Model_Blueprint::factory()->room('workshop_lv1')->name('Ausgebaute Werkstatt'), true)
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('workshop')
            ->requires_room('free')
            ->material([Model_Items_Generic_Table::cls() => 1, Model_Items_Abstract_Chair::cls() => 1])
            ->name('Werkstatt')
            ->emplaces_action()
            ->description('Die Werkstatt kann mit vielerlei Werkzeugen und Geräten ausgestattet werden, die für alltägliche Bastelarbeiten erforderlich sind.')
            ->energy(5)
            ->space(5)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('workshop_furniture')
            ->requires_room('workshop')
            ->replace_room_satisfaction(false)
            ->material([Model_Items_Generic_Sum::cls() => 5])
            ->provide_room(['workshop_lv1'])
            ->name('Möbel-Werkstatt')
            ->description('In dieser spezialisierten Werkstatt kannst du praktische Einrichtungsgegenstände für dein Versteck produzieren.')
            ->energy(15)
            ->space(10)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('workshop_electro')
            ->requires_room('workshop')
            ->requires_room_tag('inside')
            ->replace_room_satisfaction(false)
            ->provide_room(['workshop_lv1'])
            ->material([Model_Items_Generic_Lamp::cls() => 1, Model_Items_Generic_Electro::cls() => 3, Model_Items_Energy::cls() => 5])
            ->name('Elektrotechnik-Werkstatt')
            ->description('Du leidest an einem Mangel an elektronischen Geräten wie Smartphones, Taschenlampen und Todeslasern? Dann solltest du in diese spezialisierte Werkstatt investieren!')
            ->energy(15)
            ->space(2)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('workshop_bio')
            ->requires_room('workshop')
            ->requires_room_tag('inside')
            ->replace_room_satisfaction(false)
            ->material([Model_Items_Generic_Electro::cls() => 2, Model_Items_Nutrient::cls() => 2, Model_Items_Bottle::cls() => 2])
            ->provide_room(['workshop_lv1'])
            ->name('Biotechnik-Werkstatt')
            ->description('Diese spezialisierte Werkstatt hilft dir, unnützen Bioabfall in colle und moralisch definitiv nicht fragwürdige Gadgets umzuwandeln.')
            ->energy(15)
            ->space(6)
    )
    ->pop_stack()

    ;