<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // External stuff
    ->add_blueprints(Model_Blueprint::factory()->id('hideout_slot')->name('Sicheres Versteck'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('outside')->name('Bebaubarer Aussenbereich'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('outside_space')->name('Großflächiger Aussenbereich'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('impaler')->name('Vorbereitete Fallgruben'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('slot_epic')->name('Bauplatz für epische Projekte'), true)

    ->add_blueprints(Model_Blueprint::factory()->requires_room('invalid')->room('free')->name('Unbenutzter Raum'), true)
    ->add_blueprints(Model_Blueprint::factory()->requires_room('invalid')->room('used')->name('Eingerichteter Raum'), true)

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier(Model_Blueprint::BP_MOD_ENERGY, function($pl,$pre,$e) { /** @var Model_Player $pl */
            $mod = 1;
            if (Tool_Scripts::get_timeofday($pl) == 'morning') $mod -= 0.25;    // Daytime bonus
            if ($pl->get_status()->retrieve('tr_handyman')) $mod -= 0.1;    // Handyman Bonus
            return max(min(1,$e),floor($e*$mod));
        });
    })

    /** KITCHEN */

    ->add_blueprints(Model_Blueprint::factory()->room('kitchen_lv1')->name('Ausgebaute Küche'), true)
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('kitchen')
            ->requires_room('free')
            ->material(['Model_Items_Generic_Table' => 1])
            ->name('Küche')
            ->emplaces('Model_Items_Virtual_Location_Room_Kitchen')
            ->description('In einer Küche kannst du aus diversen Gegenständen Nahrungsmittel zubereiten. Für deren Geschmack wird jedoch keine Garantie übernommen...')
            ->energy(5)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('kitchen_slaughter')
            ->requires_room('kitchen')
            ->provide_room(['kitchen','kitchen_lv1'])
            ->name('Schlachthaus')
            ->description('Es gibt überraschend viele Dinge, die sich zwecks Nahrungsgewinnung schlachten lassen (meist jedoch unfreiwillig). Eine Vorraussetzung dafür ist natürlich der Bau eines Schlachthauses.')
            ->energy(15)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('kitchen_meth')
            ->requires_room('kitchen')
            ->provide_room(['kitchen','kitchen_lv1'])
            ->name('Drogenküche')
            ->description('Warum sollte man nur Nahrungsmittel kochen? Mit ein wenig zusätzlicher Ausrüstung kannst du auch leckere 5-Sterne-Drogen zubereiten!')
            ->energy(15)
    )

    /** WORKSHOP */

    ->add_blueprints(
        Model_Blueprint::factory()
            ->room('workshop')
            ->requires_room('free')
            ->name('Werkstatt')
            ->emplaces('Model_Items_Virtual_Location_Maker')
            ->description('Die Werkstatt kann mit vielerlei Werkzeugen und Geräten ausgestattet werden, die für alltägliche Bastelarbeiten erforderlich sind.')
            ->energy(5)
    )

    ->pop_stack()

    ;