<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // ++ STACK -> All blueprints below can be produced indefinitely
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0);})

    // ++ STACK -> All blueprints below need the basic kitchen
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('kitchen')->category('Küche');})

    // BASIC KITCHEN
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:soft')
            ->message('Nur wenige wissen, dass sich Softdrinks in klares Wasser umwandeln lassen, indem man einfach zwei von ihnen zusammenmischt. Gut, dass du in Chemie immer so gut aufgepasst hast!')
            ->energy(1)
            ->material([Model_Items_Softdrink::cls() => 2])
            ->produces([Model_Items_Generic_Water0::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:pksoup')
            ->message('Zerstampfen, verrühren, würzen. Für dieses Rezept muss man kein Meisterkoch sein, und man kann es in allen Lebenslagen anwenden (Kürbisse zubereiten, Gespräche mit dem Finanzamt etc) ')
            ->material([Model_Items_Pumpkin::cls() => 1, Model_Items_Generic_Spice::cls() => 1, Model_Items_Generic_Waterv::cls() => 1])
            ->energy(10)
            ->produces([Model_Items_Pumpkinsoup::cls() => 5])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:coffee')
            ->requires_local('ktc2')
            ->message('Erschöpft vom Zombieapokalypse-Alltag setzt du dir erstmal eine schöne Kanne Kaffee auf.')
            ->material([Model_Items_Coffee::cls() => 1, Model_Items_Energy::cls() => 2])
            ->produces([Model_Items_Coffee2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:water')
            ->requires_local('ktc2')
            ->message('Das Wasser in deiner Flasche schaut dich mit großen, traurigen Augen an - aber das hilft nicht viel. Eiskalt kochst du es auf 100° und tötest so alles Leben darin ab!')
            ->material([Model_Items_Generic_Waterv::cls() => 1, Model_Items_Energy::cls() => 2])
            ->produces([Model_Items_Generic_Water0::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:nom1')
            ->requires_local('ktc3')
            ->message('Wenn man zwei Grundnahrungsmittel kombiniert, kann man unter umständen ein ganz neues Nahrungsmittel erschaffen. Diesen Vorgang nennt man "kochen", und du hast ihn soeben erfolgreich durchgeführt.')
            ->material([Model_Items_Basefood::cls() => 2])
            ->energy(10)
            ->produces([Model_Items_Nom::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:meat')
            ->requires_local('ktc4')
            ->name('Abgekochte Knochen')
            ->message('Nachdem du die Knochen mit Fleisch in den Ofen gelegt hast, ist deine Küche erfüllt von .... leckerem .... Geruch. Aber wenigstens kannst du die Knochen nun essen, ohne dir eine Vergiftung zuzuziehen.')
            ->energy(10)
            ->material([Model_Items_Rawmeat::cls() => 4, Model_Items_Energy::cls() => 2])
            ->produces([Model_Items_Rawmeat2::cls() => 4])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:nom2')
            ->requires_local('ktc4')
            ->message('Eine leckere Speise ist nur halb so gut, wenn sie kalt und ungewürzt ist. Du streust also ein paar Gewürze drüber und lässt das ganze eine Weile im Ofen schmoren - voilá, du hast deine Speise noch leckerer gemacht!')
            ->material([Model_Items_Nom::cls() => 1, Model_Items_Generic_Spice::cls() => 1])
            ->energy(10)
            ->produces([Model_Items_Nom2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:nomv1')
            ->requires_local('ktc4')
            ->material(Model_Items_Basefood::cls(),         2, null, 5)
            ->material(Model_Items_Basefood::cls(),         1, null, 7)
            ->material(Model_Items_Generic_Spice::cls(),    1, null, 0)
            ->energy(15)
            ->produces(Model_Items_Nomv::cls(), 1, 0)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:nomv2')
            ->requires_local('ktc4')
            ->material(Model_Items_Basefood::cls(),         1, null, 0)
            ->material(Model_Items_Basefood::cls(),         1, null, 1)
            ->material(Model_Items_Basefood::cls(),         1, null, 2)
            ->material(Model_Items_Generic_Spice::cls(),    1, null, 0)
            ->energy(15)
            ->produces(Model_Items_Nomv::cls(), 1, 1)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:nomv3')
            ->requires_local('ktc2')
            ->material(Model_Items_Basefood::cls(),         1, null, 0)
            ->material(Model_Items_Basefood::cls(),         1, null, 3)
            ->material(Model_Items_Basefood::cls(),         2, null, 4)
            ->energy(5)
            ->produces(Model_Items_Nomv::cls(), 1, 2)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:nomv4')
            ->requires_local('ktc4')
            ->material(Model_Items_Basefood::cls(),         2, null, 5)
            ->material(Model_Items_Rawmeat2::cls(),         1, null, 0)
            ->material(Model_Items_Generic_Spice::cls(),    1, null, 0)
            ->energy(15)
            ->produces(Model_Items_Nomv::cls(), 1, 3)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:k_frz_body1')
            ->requires_local('ktc_cool')
            ->material([Model_Items_Body::cls() => 1, Model_Items_Energy::cls() => 1])
            ->produces([Model_Items_BodyF::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:k_frz_body2')
            ->requires_local('ktc_cool')
            ->material([Model_Items_Body2::cls() => 1, Model_Items_Energy::cls() => 1])
            ->produces([Model_Items_BodyF2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:k_frz_body3')
            ->requires_local('ktc_cool')
            ->material([Model_Items_Body3::cls() => 1, Model_Items_Energy::cls() => 1])
            ->produces([Model_Items_BodyF3::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:k_frz_snow')
            ->requires_local('ktc_cool')
            ->material([Model_Items_Generic_Waterb::cls() => 1, Model_Items_Energy::cls() => 1])
            ->produces([Model_Items_Snowball::cls() => 10])
    )

    // SLAUGHTERHOUSE
    // ++ STACK -> All blueprints below need the SLAUGHTERHOUSE kitchen
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('kitchen_slaughter');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:body1')
            ->name('Ausgenommene Leiche')
            ->message('Du benutzt deine Machete, um die Leiche in kleine Stücke zu schneiden. Das macht sie zwar nicht genießbarer, aber zumindest handlicher.')
            ->energy(15)
            ->material([Model_Items_Body::cls() => 1])
            ->produces([Model_Items_Rawmeat::cls() => 8, Model_Items_Generic_Waterb::cls() => 3])
            ->effect(
                Model_Effect::factory()
                    ->achieve(Model_Achievement::MA_BLOODSUCKER)
            )
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:body1_org')
            ->requires_local('ktc3')
            ->name('Organentnahme')
            ->message('Mit dem richtigen Werkzeug kannst du aus dieser Leiche nicht nur Fleisch herausschneiden, sondern auch ein transplantations-geeignetes Organ!')
            ->energy(15)
            ->material([Model_Items_Body::cls() => 1])
            ->produces([Model_Items_Rawmeat::cls() => 2, Model_Items_Organ::cls() => 2, Model_Items_Generic_Waterb::cls() => 3])
            ->effect(
                Model_Effect::factory()
                    ->achieve(Model_Achievement::MA_BLOODSUCKER)
            )
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:body3')
            ->name('Ausgenommener Zombie')
            ->message('Du benutzt deine Machete, um die Leiche in kleine Stücke zu schneiden. Das macht sie zwar nicht genießbarer, aber zumindest handlicher.')
            ->energy(25)
            ->material([Model_Items_Body2::cls() => 1])
            ->produces([Model_Items_Rawmeat3::cls() => 7, Model_Items_Generic_Waterb::cls() => 4])
            ->effect(
                Model_Effect::factory()
                    ->achieve(Model_Achievement::MA_BLOODSUCKER)
            )
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:body3_org')
            ->requires_local('ktc3')
            ->name('Organentnahme')
            ->message('Mit dem richtigen Werkzeug kannst du aus dieser Leiche nicht nur Fleisch herausschneiden, sondern auch ein transplantations-geeignetes Organ!')
            ->energy(25)
            ->material([Model_Items_Body2::cls() => 1])
            ->produces([Model_Items_Rawmeat3::cls() => 2, Model_Items_Organ3::cls() => 2, Model_Items_Generic_Waterb::cls() => 3])
            ->effect(
                Model_Effect::factory()
                    ->achieve(Model_Achievement::MA_BLOODSUCKER)
            )
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:body4')
            ->name('Ausgenommener Tierkadaver')
            ->message('Meh... immer noch besser als Fastfood.')
            ->energy(10)
            ->material([Model_Items_Body3::cls() => 1])
            ->produces([Model_Items_Rawmeat::cls() => 4, Model_Items_Generic_Waterb::cls() => 1])
            ->effect(
                Model_Effect::factory()
                    ->achieve(Model_Achievement::MA_BLOODSUCKER)
            )
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:body4_org')
            ->requires_local('ktc3')
            ->name('Organentnahme')
            ->message('Mit dem richtigen Werkzeug kannst du aus dieser Leiche nicht nur Fleisch herausschneiden, sondern auch ein transplantations-geeignetes Organ!')
            ->energy(20)
            ->material([Model_Items_Body3::cls() => 1])
            ->produces([Model_Items_Rawmeat::cls() => 1, Model_Items_Organ2::cls() => 2, Model_Items_Generic_Waterb::cls() => 1])
            ->effect(
                Model_Effect::factory()
                    ->achieve(Model_Achievement::MA_BLOODSUCKER)
            )
    )

    ->pop_stack()

    // METHLAB
    // ++ STACK -> All blueprints below need the METHLAB kitchen
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('kitchen_meth');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:cspray1')
            ->energy(5)
            ->material(Model_Items_Generic_Spraycan::cls(), 1, null, 1)
            ->material(Model_Items_Chem::cls(), 2, null, 0)
            ->material(Model_Items_Chem::cls(), 1, null, 1)
            ->produces(Model_Items_Spray::cls(), 1, 0)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:cspray2')
            ->energy(5)
            ->material(Model_Items_Generic_Spraycan::cls(), 1, null, 1)
            ->material(Model_Items_Chem::cls(), 2, null, 0)
            ->material(Model_Items_Chem::cls(), 3, null, 11)
            ->produces(Model_Items_Spray::cls(), 1, 1)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:cspray3')
            ->energy(5)
            ->material(Model_Items_Generic_Spraycan::cls(), 1, null, 1)
            ->material(Model_Items_Chem::cls(), 2, null, 2)
            ->material(Model_Items_Chem::cls(), 3, null, 3)
            ->produces(Model_Items_Spray::cls(), 1, 2)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:cspray4')
            ->energy(5)
            ->material(Model_Items_Generic_Spraycan::cls(), 1, null, 1)
            ->material(Model_Items_Chem::cls(), 2, null, 2)
            ->material(Model_Items_Chem::cls(), 3, null, 10)
            ->produces(Model_Items_Spray::cls(), 1, 3)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:cspray5')
            ->energy(5)
            ->material(Model_Items_Generic_Spraycan::cls(), 1, null, 1)
            ->material(Model_Items_Chem::cls(), 2, null, 4)
            ->material(Model_Items_Chem::cls(), 2, null, 1)
            ->material(Model_Items_Chem::cls(), 2, null, 5)
            ->produces(Model_Items_Spray::cls(), 1, 4)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:cspray6')
            ->energy(5)
            ->material(Model_Items_Generic_Spraycan::cls(), 1, null, 1)
            ->material(Model_Items_Chem::cls(), 2, null, 4)
            ->material(Model_Items_Chem::cls(), 2, null, 7)
            ->material(Model_Items_Chem::cls(), 2, null, 11)
            ->produces(Model_Items_Spray::cls(), 1, 5)
    )

    ->pop_stack()

    // -- STACK -> All blueprints below NO LONGER need the basic kitchen
    ->pop_stack()

    // ++ STACK -> All blueprints below need the basic workbench and benefit from suspender upgrade
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('workshop')->category('Werkbank')->add_modifier(Model_Blueprint::BP_MOD_ENERGY, function($pl,
        /** @noinspection PhpUnusedParameterInspection */
        $pre,$e,$room) {/** @var Model_Player $pl */
        /** @var Model_Player $pl */
        /** @var Model_Room $room */
        $mod = 1;
        if ($room->has_content('manuspd')) $mod -= 0.5;           // Suspender Bonus
        if ($pl->get_status()->retrieve('tr_handyman')) $mod -= 0.1;                     // Handyman Bonus

        return max(min(1,$e),floor($e*$mod));
    });})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:bike')
            ->message('Das war einfacher als du dachtest - dein Fahrrad ist nun wieder einsatzbereit!')
            ->energy(15)
            ->material([Model_Items_Generic_Bike::cls() => 1, Model_Items_Generic_Sum::cls() => 2, Model_Items_Generic_Belt::cls() => 1])
            ->produces([Model_Items_Generic_Bike2::cls() => 1])
    )

    // ++ STACK -> All blueprints below can only be built by a survivalist
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->show_condition(function($player) {/** @var Model_Player $player */return $player->job(1060);});})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:srv_sum')
            ->name('Selbstgebaute Kleinteile')
            ->message('Großartig - andere hätten diese Kleinteile mühsam zusammensuchen müssen, du kannst sie einfach selbst herstellen!')
            ->energy(25)
            ->material([Model_Items_Generic_Crmetal::cls() => 5])
            ->produces([Model_Items_Generic_Sum::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:srv_mat')
            ->name('Flickenmatratze')
            ->message('Keine Ahnung wozu du als Survivalist überhaupt eine Matratze brauchst... aber gut, wenn du Spaß an Bastelarbeit hast.')
            ->energy(25)
            ->material([Model_Items_Generic_Cloth::cls() => 8])
            ->produces([Model_Items_Generic_Bed::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:srv_tbl')
            ->name('Provisorischer Tisch')
            ->message('Wer braucht schon EKEA? Dieser Tisch hat eine mindestens genauso fragwürdige Qualität, und er ist aus echtem undefinierbaren Holz!')
            ->energy(25)
            ->material([Model_Items_Generic_Wood::cls() => 7])
            ->produces([Model_Items_Generic_Table::cls() => 1])
    )

    // -- STACK -> All blueprints below can NO LONGER only be built by a survivalist
    ->pop_stack()

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:tech')
            ->message('Dank deiner beeindruckenden Techniker-Ausbildung hast du es geschafft, aus Schrott dieses hochpräzise Vermessungsinstrument zusammenzubauen.')
            ->energy(10)
            ->material([Model_Items_Generic_Metal::cls() => 1, Model_Items_Generic_Sum::cls() => 1, Model_Items_Generic_Tube::cls() => 1])
            ->produces([Model_Items_Generic_Lasermapper::cls() => 1])
            ->show_condition(function($p) {
                /** @var Model_Player $p */
                return $p->job(3030);
            })
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:crwood1')
            ->message('Gäbe es einen Gott für Recycling, er wäre sicherlich stolz auf dich!')
            ->energy(6)
            ->material([Model_Items_Generic_Crwood::cls() => 4])
            ->produces([Model_Items_Generic_Wood::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:crwood2')
            ->message('Gäbe es einen Gott für Recycling, er wäre sicherlich stolz auf dich!')
            ->material([Model_Items_Generic_Crwood::cls() => 4, Model_Items_Energy::cls() => 2])
            ->produces([Model_Items_Generic_Wood::cls() => 3])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:crmetal1')
            ->message('Gäbe es einen Gott für Recycling, er wäre sicherlich stolz auf dich!')
            ->energy(6)
            ->material([Model_Items_Generic_Crmetal::cls() => 4])
            ->produces([Model_Items_Generic_Metal::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:crmetal2')
            ->message('Gäbe es einen Gott für Recycling, er wäre sicherlich stolz auf dich!')
            ->material([Model_Items_Generic_Crmetal::cls() => 4, Model_Items_Energy::cls() => 2])
            ->produces([Model_Items_Generic_Metal::cls() => 3])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:bolts1')
            ->message('Wäre dies ein Vampirspiel, so wärst du mit diesen Bolzen perfekt ausgerüstet, um Dracula gegenüber zu treten. Leider ist dies ein Zombiespiel, also wirst du wohl doch zur Armbrust greifen müssen...')
            ->energy(2)
            ->material([Model_Items_Generic_Crwood::cls() => 1])
            ->produces([Model_Items_Bolts::cls() => 3])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:bolts2')
            ->message('Wäre dies ein Vampirspiel, so wärst du mit diesen Bolzen perfekt ausgerüstet, um Dracula gegenüber zu treten. Leider ist dies ein Zombiespiel, also wirst du wohl doch zur Armbrust greifen müssen...')
            ->energy(3)
            ->material([Model_Items_Generic_Wood::cls() => 1])
            ->produces([Model_Items_Bolts::cls() => 10])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:bbag1')
            ->message('Ein Stich hier.... ein Stich dort... Fertig! Dieser stylische Leichensack wird deine Transportprobleme zumindest im Bezug auf Leichen für immer lösen!')
            ->energy(15)
            ->material([Model_Items_Generic_Cloth::cls() => 8])
            ->produces([Model_Items_Bodybag::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:bbag2')
            ->name('Leichensack provisorisch flicken')
            ->message('Du fühlst sich wie eine Art makabrer Modedesigner! Ein paar Sticke mit der Nadel, schon ist dieser hässliche Riss fast nicht mehr zu sehen.')
            ->energy(5)
            ->material([Model_Items_Generic_Cloth::cls() => 3, Model_Items_Bodybag4::cls() => 1])
            ->produces([Model_Items_Bodybag2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:bbag3')
            ->name('Leichensack gründlich flicken')
            ->message('Du fühlst sich wie eine Art makabrer Modedesigner! Ein paar Sticke mit der Nadel, schon ist dieser hässliche Riss nicht mehr zu sehen. Und ein paar potentielle Schwachstellen hast du gleich mit ausgebessert! Bravo!')
            ->energy(9)
            ->material([Model_Items_Generic_Cloth::cls() => 5, Model_Items_Bodybag4::cls() => 1])
            ->produces([Model_Items_Bodybag::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:skel')
            ->name('Sortierte Knochen')
            ->message('Endlich kannst du deine destruktiven Energien mal an was anderem als an Zombies ausleben. Aus irgend einem Grund bereitet dir das Auseinandernehmen dieses Skeletts eine merkwürdige Befriedigung...')
            ->energy(10)
            ->material([Model_Items_Generic_Bone3::cls() => 1])
            ->produces([Model_Items_Bone::cls() => 8, Model_Items_Bone2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:bbbat1')
            ->message('Mit diesem Schläger fährst du keine Homerun-Rekorde mehr ein... Dafür ist er wesentlich Effektiver im Bereich "Zombieverstümmelung".')
            ->energy(20)
            ->material([Model_Items_Bat::cls() => 1, Model_Items_Generic_Ducttape::cls() => 2, Model_Items_Generic_Crmetal::cls() => 4])
            ->produces([Model_Items_Bat2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:crossbow')
            ->message('Du hast eine Armbrust hergestellt. Wie wärs, wenn du direkt mal mit Zielübungen auf ein paar Zombies beginnst?')
            ->energy(50)
            ->material([Model_Items_Generic_Wire::cls() => 1, Model_Items_Generic_Wood::cls() => 2, Model_Items_Generic_Metal::cls() => 1, Model_Items_Generic_Sum::cls() => 1])
            ->produces([Model_Items_Crossbow::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:batgun1')
            ->message('Mit ein paar kleinen Verbesserungen kann man die Effektivität eines Batteriewerfers ungemein erhöhen. Mit der neuen Ladevorrichtung sparst du im Kampf viel Zeit, die du wiederum in das Abschlachten weiterer Zombies investieren kannst.')
            ->energy(5)
            ->material([Model_Items_Generic_Ducttape::cls() => 1, Model_Items_Generic_Tube::cls() => 1, Model_Items_Batgun::cls() => 1])
            ->produces([Model_Items_Batgun2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:batgun2')
            ->message('Mit ein paar kleinen Verbesserungen kann man die Effektivität eines Batteriewerfers ungemein erhöhen. Der neue Druckregler passt die Abschussgeschwindigkeit genau der Entfernung an und erhöht so deine Treffsicherheit. Mit ein wenig Glück kannst du mit einer Baterie sogar zwei Zombies erwischen!')
            ->energy(5)
            ->material([Model_Items_Generic_Pressure::cls() => 1, Model_Items_Generic_Sum::cls() => 2, Model_Items_Generic_Tube::cls() => 1, Model_Items_Batgun2::cls() => 1])
            ->produces([Model_Items_Batgun3::cls() => 1])
            ->effect(Model_Effect::factory()
                ->achieve(Model_Achievement::MA_BUILD_MKII)
            )
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:splintergun1')
            ->message('Was kann man mit einer verrückten Waffe machen? Sie NOCH verrückter machen, natürlich! Was denn sonst?')
            ->energy(25)
            ->material([Model_Items_Generic_Pressure::cls() => 1, Model_Items_Generic_Sum::cls() => 2, Model_Items_Generic_Tube::cls() => 1, Model_Items_Generic_Metal::cls() => 3, Model_Items_Splintergun::cls() => 1])
            ->produces([Model_Items_Splintergun2::cls() => 1])
            ->effect(Model_Effect::factory()
                ->achieve(Model_Achievement::MA_BUILD_MKII)
            )
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:aquagun1')
            ->message('Es ist wirklich überhaupt nicht bizarr, wenn erwachsene Überlebende einer Apokalypse durch die Ruinen der Zivilisation rennen und mit militärischen Wasserpistolen um sich spritzen! Hört auch zu lachen!')
            ->energy(5)
            ->material([Model_Items_Generic_Pressure::cls() => 1, Model_Items_Generic_Sum::cls() => 1, Model_Items_Generic_Tube::cls() => 4, Model_Items_Watergun::cls() => 1])
            ->produces([Model_Items_Watergun2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:clothes_impr')
            ->message('Normalerweise würde kein Mensch so etwas machen - aber in der aktuellen Situation ist es tatsächlich notwendig, seine Bequemlichkeit zugunsten von etwas mehr Sicherheit zu opfern.')
            ->energy(15)
            ->material([Model_Items_Clothes::cls() => 1, Model_Items_Generic_Cloth::cls() => 1, Model_Items_Generic_Metal::cls() => 1, Model_Items_Generic_Crmetal::cls() => 4])
            ->produces([Model_Items_Clothes2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:clothes_fix')
            ->name('Kleidung nähen')
            ->message('Es sieht etwas zusammengeschustert aus... aber wenigstens musst du nun nicht mehr in Unterwäsche herumlaufen.')
            ->energy(5)
            ->material([Model_Items_Generic_Clothes::cls() => 1, Model_Items_Generic_Cloth::cls() => 2])
            ->produces([Model_Items_Clothes::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:shield1')
            ->message('Hierfür muss man wahrlich kein Meister der Handwerkskunst sein. Du hast einen Holzkistendeckel zusammengebaut.')
            ->energy(10)
            ->material([Model_Items_Generic_Wood::cls() => 4, Model_Items_Generic_Ducttape::cls() => 1])
            ->produces([Model_Items_Shield::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:shield2')
            ->message('Mit ein bisschen mehr Holz (und Schrauben anstelle von Klebeband) hast du deinen Holzkistendeckel stabilisiert.')
            ->energy(20)
            ->material([Model_Items_Shield::cls() => 1, Model_Items_Generic_Wood::cls() => 4, Model_Items_Generic_Sum::cls() => 1])
            ->produces([Model_Items_Shield2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:helmet')
            ->message('Mit ein bisschen Metall (und Schrauben anstelle von Klebeband) hast du deinen Fahrradhelm verbessert.')
            ->energy(20)
            ->material([Model_Items_Helmet::cls() => 1, Model_Items_Generic_Metal::cls() => 4, Model_Items_Generic_Sum::cls() => 1])
            ->produces([Model_Items_Helmet2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:pbomb')
            ->message('Vorsichtig füllst du das Schwarzpulver in eine Plastiktüte... BINGO! Perfekte Schwarzpulverbombe! Dieses Teil wird dir sicher irgendwann einmal das Leben retten.')
            ->energy(1)
            ->material([Model_Items_Generic_Ducttape::cls() => 1, Model_Items_Generic_Gunpowder::cls() => 1, Model_Items_Generic_Plasticbag::cls() => 1])
            ->produces([Model_Items_Powderbomb::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:pkbomb')
            ->message('So ein Kürbis kann sicher toll explodieren, wenn man ihn bis zum Rand mit Schwarzpulver vollstopft! Und das Gesicht.... naja, der Kürbis hätt halt ohne einfach doof ausgesehen.')
            ->energy(10)
            ->material([Model_Items_Generic_Pumpkin::cls() => 1, Model_Items_Generic_Gunpowder::cls() => 5])
            ->produces([Model_Items_Pumpkinbomb::cls() => 1])
            ->effect(Model_Effect::factory()
                ->achieve(Model_Achievement::MA_PUMPKINHEAD)
            )
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:bandage')
            ->message('Also, so richtig hygienisch sieht das jetzt nicht aus...')
            ->energy(5)
            ->material([Model_Items_Generic_Cloth::cls() => 2, Model_Items_Whiskey::cls() => 1])
            ->produces([Model_Items_Bandage2::cls() => 1, Model_Items_Smallbottle::cls() => 1])
    )

    // ++ STACK -> All blueprints below need the furniture workshop
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('workshop_furniture');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:ripbed')
            ->name('Matratze auseinanderschneiden')
            ->message('Dieses Teil hast du eh nicht mehr gebraucht... und warum soll es rumliegen und Platz verschwenden, wenn du es einfach auseinandernehmen kannst?')
            ->energy(10)
            ->material([Model_Items_Generic_Bed::cls() => 1])
            ->produces([Model_Items_Generic_Cloth::cls() => 5])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:ripboil')
            ->name('Wasserkocher-Bauteile')
            ->message('Dieses Teil hast du eh nicht mehr gebraucht... und warum soll es rumliegen und Platz verschwenden, wenn du es einfach auseinandernehmen kannst?')
            ->energy(10)
            ->material([Model_Items_Generic_Boiler::cls() => 1])
            ->produces([Model_Items_Generic_Crmetal::cls() => 1, Model_Items_Generic_Electro::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:ripmix')
            ->name('Handmixer-Bauteile')
            ->message('Dieses Teil hast du eh nicht mehr gebraucht... und warum soll es rumliegen und Platz verschwenden, wenn du es einfach auseinandernehmen kannst?')
            ->energy(6)
            ->material([Model_Items_Generic_Mixer::cls() => 1])
            ->produces([Model_Items_Generic_Metal::cls() => 1, Model_Items_Generic_Sum::cls() => 2])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:riptable')
            ->name('Järpen-Bauteile')
            ->message('Dieses Teil hast du eh nicht mehr gebraucht... und warum soll es rumliegen und Platz verschwenden, wenn du es einfach auseinandernehmen kannst?')
            ->energy(10)
            ->material([Model_Items_Generic_Table::cls() => 1])
            ->produces([Model_Items_Generic_Wood::cls() => 5, Model_Items_Generic_Sum::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:rippressure')
            ->name('Druckregler-Bauteile')
            ->message('Dieses Teil hast du eh nicht mehr gebraucht... und warum soll es rumliegen und Platz verschwenden, wenn du es einfach auseinandernehmen kannst?')
            ->energy(30)
            ->material([Model_Items_Generic_Pressure::cls() => 1])
            ->produces([Model_Items_Generic_Metal::cls() => 1, Model_Items_Generic_Sum::cls() => 3, Model_Items_Generic_Tube::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:ripmotor')
            ->name('Motor-Bauteile')
            ->message('Dieses Teil hast du eh nicht mehr gebraucht... und warum soll es rumliegen und Platz verschwenden, wenn du es einfach auseinandernehmen kannst?')
            ->energy(50)
            ->material([Model_Items_Generic_Motor::cls() => 1])
            ->produces([Model_Items_Generic_Metal::cls() => 7, Model_Items_Generic_Electro::cls() => 3, Model_Items_Generic_Tube::cls() => 5])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:ripoven')
            ->name('Ofen-Bauteile')
            ->message('Dieses Teil hast du eh nicht mehr gebraucht... und warum soll es rumliegen und Platz verschwenden, wenn du es einfach auseinandernehmen kannst?')
            ->energy(50)
            ->material([Model_Items_Generic_Oven::cls() => 1])
            ->produces([Model_Items_Generic_Metal::cls() => 5, Model_Items_Generic_Sum::cls() => 5, Model_Items_Generic_Cloth::cls() => 2])
    )

    ->pop_stack()

    // ++ STACK -> All blueprints below need the bio workshop
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('workshop_bio');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:aug_1')
            ->energy(20)
            ->material([Model_Items_Organ::cls() => 1,Model_Items_Generic_Metal::cls() => 1, Model_Items_Generic_Electro::cls() => 1])
            ->produces([Model_Items_Augments_Class1::cls() => 1])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:aug_2')
            ->energy(20)
            ->material([Model_Items_Organ3::cls() => 1,Model_Items_Generic_Metal::cls() => 1, Model_Items_Generic_Electro::cls() => 1])
            ->produces([Model_Items_Augments_CClass1::cls() => 1])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:aug_3')
            ->energy(30)
            ->material([Model_Items_Organ::cls() => 1,Model_Items_Organ2::cls() => 2,Model_Items_Generic_Metal::cls() => 2, Model_Items_Generic_Electro2::cls() => 1, Model_Items_Generic_Sum::cls() => 2])
            ->produces([Model_Items_Augments_Class2::cls() => 1])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:aug_4')
            ->energy(30)
            ->material([Model_Items_Organ3::cls() => 1,Model_Items_Organ2::cls() => 2,Model_Items_Generic_Metal::cls() => 2, Model_Items_Generic_Electro2::cls() => 1, Model_Items_Generic_Sum::cls() => 2])
            ->produces([Model_Items_Augments_Cclass2::cls() => 1])
    )

    // -- STACK -> All blueprints below NO LONGER need the basic workbench
    ->pop_stack()

    // ++ STACK -> All blueprints below need the electronic workshop
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('workshop_electro');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:uelec1')
            ->energy(5)
            ->material([Model_Items_Generic_Electro::cls() => 2, Model_Items_Generic_Sum::cls() => 1, Model_Items_Generic_Metal::cls() => 2])
            ->produces([Model_Items_Generic_Electro2::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:uelec2')
            ->energy(5)
            ->material([Model_Items_Generic_Electro2::cls() => 2, Model_Items_Generic_Electro::cls() => 2, Model_Items_Generic_Tube::cls() => 2, Model_Items_Generic_Wire::cls() => 2])
            ->produces([Model_Items_Generic_Electro3::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:pgen')
            ->energy(25)
            ->material([Model_Items_Generic_Motor::cls() => 1, Model_Items_Generic_Electro2::cls() => 1, Model_Items_Generic_Metal::cls() => 8, Model_Items_Generic_Tube::cls() => 2, Model_Items_Generic_Sum::cls() => 6])
            ->produces([Model_Items_Generator::cls() => 1])
    )


    // -- STACK -> All blueprints below NO LONGER need the electronic workbench
    ->pop_stack()

    ->drop_stack();