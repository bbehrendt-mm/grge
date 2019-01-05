<?php defined('SYSPATH') or die('No direct access allowed.');

//ToDO Add spaces

return Model_Blueprints::factory()

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    // Hideout repair stuff
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hideout')
            ->requires_room('common_hideout')
            ->provide('hideout_slot')
            ->name('Versteck')
            ->description('Ermöglicht es dir, diesen Ort als Versteck zu nutzen.')
            ->message('Du hast so ziemlich alle Zugänge zu deinem Versteck notdürftig verbarrikadiert. Zwar ist das nur ein Anfang, aber vorerst solltest du hier sicher sein.')
            ->energy(20)
            ->material(Model_Items_Generic_Wood::cls(),2)
            ->decay(-100)
            ->category('Versteck')
    )

    // ++ STACK -> All blueprints below need the hideout
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('hideout');})

    // ++ STACK -> HIDEOUT category
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('common_hideout');})


    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Reparatur');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hideoutfx1')
            ->name('Notdürftige Reparatur')
            ->description('Repariert dein Versteck, beschleunigt jedoch auch dessen Verfall.')
            ->message('Löcher im Boden und in den Wänden? Nicht isolierte Starkstromkabel, die von der Decke hängen? Ein in Flammen stehendes Bett? Kein Problem für einen Handwerkermeister wie dich! Im Nuh ist alles notdürftig und mit minderwertigen Werkzeugen und Rohstoffen geflickt. Das wird zwar nicht allzu lange halten, aber für die nächsten paar Sekunden ist die Einsturzgefahr deines Verstecks gebannt.')
            ->steps(0)
            ->energy(20)
            ->material(Model_Items_Generic_Crwood::cls(),3)->material(Model_Items_Generic_Crmetal::cls(),3)
            ->decay(-10, 0.05)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hideoutfx2')
            ->name('Gründliche Reparatur')
            ->description('Repariert dein Versteck und reduziert den Verfall geringfügig.')
            ->message('Gut, dass du hier keinen Amateur rangelassen hast. Vorsichtig und mit Augenmaß hast du alle Mängel beseitigt - das macht dein Versteck nicht nur sicherer, sondern hält auch den weiteren Verfall auf. Zumindest für eine Weile...')
            ->steps(0)
            ->energy(50)
            ->material(Model_Items_Generic_Wood::cls(),3)->material(Model_Items_Generic_Metal::cls(),3)
            ->decay(-15, -0.02)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hideoutfx3')
            ->name('Ausbesserungsarbeiten')
            ->description('Verlangsamt den Vervall deines Verstecks stark.')
            ->message('Du hast einige Baumängel an deinem Versteck gefunden und behoben, bevor sie zum problem wurden. Das war zwar eine ziemlich anstrengende Arbeit, aber das Ergebnis war es wert!')
            ->steps(0)
            ->energy(60)
            ->material([Model_Items_Generic_Wood::cls() => 1, Model_Items_Generic_Metal::cls() => 1, Model_Items_Generic_Sum::cls() => 3])
            ->decay(0, -0.10)
    )

    ->pop_stack()
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Verteidigung');})

    // Defense
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall1')
            ->name('Barrikade (Tür)')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #1 gegen Zombieinvasionen: Mach die Tür zu!')
            ->deco(-5)
            ->energy(5)
            ->material([Model_Items_Generic_Wood::cls() => 1])
            ->defense(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall2')
            ->requires_local('defwall1')
            ->name('Barrikade (Fenster)')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #2 gegen Zombieinvasionen: Eine geschlossene Tür hilft nicht viel, wenn die Fenster noch sperrangelweit offen stehen!')
            ->deco(-7)
            ->steps(3)
            ->energy(10)
            ->material([Model_Items_Generic_Wood::cls() => 2])
            ->defense(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall3')
            ->requires_local('defwall2')
            ->name('Barrikade (Wände)')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #3 gegen Zombieinvasionen: Wenn Zombies keine Löcher in deiner Verteidigung finden, dann machen sie sich selbst welche! Verstärke also besser immer deine Wände.')
            ->deco(-3)
            ->steps(4)
            ->energy(15)
            ->material([Model_Items_Generic_Wood::cls() => 2, Model_Items_Generic_Metal::cls() => 1])
            ->defense(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall4')
            ->requires_local('defwall3')
            ->name('Barrikade (Dach)')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #4 gegen Zombieinvasionen: Wenn Zombies nicht von links, rechts, vorne und hinten kommen können, dann kommen sie eben von oben!')
            ->deco(-2)
            ->steps(3)
            ->energy(25)
            ->material([Model_Items_Generic_Wood::cls() => 2, Model_Items_Generic_Metal::cls() => 2, Model_Items_Generic_Sum::cls() => 2])
            ->defense(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall5')
            ->requires_local('defwall4')
            ->name('Barrikaden')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #4 gegen Zombieinvasionen: Wenn Zombies nicht von links, rechts, vorne und hinten kommen können, dann kommen sie eben von oben!')
            ->deco(-10)
            ->steps(0)
            ->energy(50)
            ->material([Model_Items_Generic_Metal::cls() => 4,Model_Items_Generic_Table::cls() => 2, Model_Items_Generic_Ducttape::cls() => 4])
            ->defense(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defbat')
            ->name('Stationäres Batteriegeschütz')
            ->description('Ermöglicht es, bei einer Belagerung Zombies durch Batterien zu töten.')
            ->message('Say hello to my little friend!')
            ->energy(20)
            ->material([Model_Items_Generic_Sum::cls() => 1, Model_Items_Generic_Tube::cls() => 2, Model_Items_Generic_Metal::cls() => 1])

    )
    ->pop_stack()

    // -- ++ STACK -> BEDROOM category
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('bedroom');})


    // Bedroom
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedrwake')
            ->name('Alarmdraht')
            ->description('Weckt alle schlafenden Spieler beim Eindringen von Zombies. Wird beim Einsatz zerstört.')
            ->message('Dieser Alarmdraht macht ordentlich Lärm, wenn Zombies im Begriff sind, dein Versteck zu attackieren. Von jetzt an brauchst du keine Angst mehr zu haben, deinen eigenen Tod zu verschlafen.')
            ->energy(1)
            ->material([Model_Items_Generic_Wire::cls() => 1, Model_Items_Generic_Crmetal::cls() => 2])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedrlights')
            ->requires_local('bedr1')
            ->name('Nachtlicht')
            ->description('Reduziert die Einschlafzeit.')
            ->message('Endlich brauchst du dich im Dunklen nicht mehr zu fürchten - dieses neue Nachtlicht hilft dir beim Einschlafen und vertreibt schlimme Träume.')
            ->deco(2)
            ->energy(1)
            ->material([Model_Items_Generic_Lamp::cls() => 1, Model_Items_Energy::cls() => 1])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedr1')
            ->name('Schlafecke')
            ->description('Verbessert Regeneration von Energie und Müdigkeit beim Schlafen. Ermöglicht außerdem die Regeneration von Gesundheit beim Schlafen.')
            ->message('Endlich musst du nicht mehr auf dem Boden schlafen - mit diesem neuen Bett hat dein Versteck nun endlich die Behaglichkeit einer simplen Crackhütte gewonnen!')
            ->deco(2)
            ->energy(5)
            ->material(Model_Items_Generic_Bed::cls(),1)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedr2')
            ->requires_local('bedr1')
            ->name('Kuschelige Schlafecke')
            ->description('Verbessert Regeneration von Energie, Müdigkeit und Gesundheit beim Schlafen.')
            ->message('Mit einer Decke und einem Teddy ist dein Bett gleich viel kuscheliger. Deine Schlafecke sieht jetzt schon richtig gemütlich aus!')
            ->deco(1)
            ->energy(5)
            ->material([Model_Items_Generic_Teddy::cls() => 1, Model_Items_Generic_Cloth::cls() => 3])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedr3')
            ->requires_local('bedr2')
            ->name('Kingsize-Bett')
            ->description('Verbessert Regeneration von Energie, Müdigkeit und Gesundheit beim Schlafen.')
            ->message('Dank deinem neuen KingSize-Bett hast du nun extra viel Platz, dich Nachts vor Angst in deinem Bett herumzuwälzen.')
            ->deco(1)
            ->energy(5)
            ->space(5)
            ->material([Model_Items_Generic_Bed::cls() => 1, Model_Items_Generic_Cloth::cls() => 3])
    )

    // -- ++ STACK -> SITTING category
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('community');})

    // Sitting area
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('sofa1')
            ->name('Sitzecke')
            ->description('Erhöht bei Benutzung die Energieregeneration. Der Effekt verstärkt sich, wenn die Sitzecke von mehreren Spielern verwendet wird.')
            ->message('Mit dieser Sitzecke kannst du dich nun endlich vernünftig entspannen ohne dich immer gleich ins Bett legen zu müssen.')
            ->deco(2)
            ->energy(3)
            ->material([Model_Items_Abstract_Chair::cls() => 3])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('sofa2')
            ->requires_local('sofa1')
            ->name('Sesselecke')
            ->description('Verbessert die Regenerationswirkung der Sitzecke. Verstärkt außerdem die Effekte beim Lesen von Büchern.')
            ->message('Diese Sessel sehen ein wenig... eigenwillig aus. Aber zumindest sind sie bequem - mehr kann man doch nun wirklich nicht verlangen. Wobei... ein Getränkehalter wäre natürlich schön...')
            ->deco(1)
            ->energy(25)
            ->material([Model_Items_Abstract_Chair::cls() => 1, Model_Items_Generic_Bed::cls() => 1, Model_Items_Generic_Cloth::cls() => 4])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bkcase')
            ->name('Bücherregal')
            ->description('Kann mit Büchern gefüllt werden, um den Dekorationswert des Verstecks zu verbessern.')
            ->energy(15)
            ->deco(2)
            ->material([Model_Items_Generic_Wood::cls() => 6])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bkcase_use')
            ->name('Buch einlagern')
            ->requires('bkcase')
            ->steps(20)
            ->description('Legt ein Buch ins Bücherregal.')
            ->deco(4)
            ->material([Model_Items_Book::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('curtains')
            ->name('Vorhänge')
            ->steps(3)
            ->description('Stattet dein Versteck mit hübschen Vorhängen aus und verbessert so den Dekorationswert.')
            ->deco(5)
            ->material([Model_Items_Generic_Cloth::cls() => 4])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bottlecol')
            ->name('Flaschensammlung')
            ->description('Nichts schmückt eine Wohnung mehr als ein riesiger Haufen leerer Bierflaschen.')
            ->deco(10)
            ->material(Model_Items_Smallbottle::cls(), 6, function($i) {/** @var Model_Items_Smallbottle $i */ return $i->count() === 0;})
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('garland_ca')
            ->provide('garland')
            ->name('Kapitalistische Girlande')
            ->description('Stelle deinen Reichtum mit dieser dekorativen Girlande zur Schau.')
            ->deco(15)
            ->material([Model_Items_Money::cls() => 5, Model_Items_Generic_Wire::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('garland_ma')
            ->provide('garland')
            ->name('Makabere Girlande')
            ->description('Es gibt nichts, aus dem man besser eine dekorative Girlande bauen kann als abgenagte Knochen! ... moment ...')
            ->deco(15)
            ->material([Model_Items_Bone::cls() => 5, Model_Items_Generic_Wire::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('garland_co')
            ->provide('garland')
            ->name('Elektrisierende Girlande')
            ->description('Diese hübsch glitzernde Girlande wertet dein Versteck dekorativ auf. Pass nur auf, dass dir keine Batteriesäure auf den Kopf tropft...')
            ->deco(15)
            ->material([Model_Items_Battery::cls() => 15, Model_Items_Generic_Wire::cls() => 1])
    )

    // -- ++ STACK -> GENERATOR category
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('utilities');})

    // Generator
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('gen1')
            ->name('Notstrom-Aggregat')
            ->description('Ermöglicht es, Strom aus Batterien zu gewinnen.')
            ->message('Endlich verfügst du über ein Notstrom-Aggregat, jetzt musst du nicht mehr im Dunkeln fernsehen! Yuhuu!')
            ->deco(-5)
            ->energy(15)
            ->material([Model_Items_Generic_Electro::cls() => 2, Model_Items_Generic_Metal::cls() => 5, Model_Items_Generic_Tube::cls() => 1])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('gen2')
            ->requires_local('gen1')
            ->name('Diesel-Generator')
            ->description('Ermöglicht es, Strom aus Energie und Benzinkanistern zu gewinnen.')
            ->message('Alle paar Minuten neue Batterien einzulegen kann schon nerven. Glücklicherweise kannst du diesem Problem mit einem Kanister Benzin vorsorgen. Einziger Haken: Du brauchst einen Kanister Benzin ...')
            ->deco(-5)
            ->energy(15)
            ->material([Model_Items_Generic_Motor::cls() => 1, Model_Items_Generic_Sum::cls() => 5, Model_Items_Generic_Metal::cls() => 2])
    )

    // -- ++ STACK -> DEFENSE LINE category
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('outside_defense');})


    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('deffence1')
            ->provide('fence')
            ->name('Holzzaun')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Nichts hält Zombies eher ab als ein weißer, frisch lackierter Holzzaun! Glaubst du nicht? Probier es halt aus!')
            ->deco(10)
            ->energy(30)
            ->material([Model_Items_Generic_Wood::cls() => 10, Model_Items_Generic_Ducttape::cls() => 2])
            ->defense(10)
            ->space(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('deffence2')
            ->provide('fence')
            ->name('Makaberer Zaun')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Was tun, wenn sich die Knochen im Lagerraum langsam stapeln? Bau einen Zaun damit! Das ist eine gute Beschäftigungstherapie und sieht einfach megacool aus. Achja, Zombies kannst du auf die Art auch von deinem Rasen fernhalten.')
            ->deco(-5)
            ->energy(30)
            ->material([Model_Items_Bone::cls() => 20, Model_Items_Generic_Ducttape::cls() => 4])
            ->defense(10)
            ->space(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('deftrench')
            ->name('Graben')
            ->global_blocking(true)
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Puuh, endlich fertig. Es war ein hartes Stück Arbeit, aber du hast es geschafft einen mehrere Meter tiefen Graben um das Versteck zu schaufeln. Den werden die Zombies niemals überwinden können - es sei denn, es fallen genug Zombies hinein, dass die restlichen einfach drüberlaufen können...')
            ->energy(95)
            ->defense(15)
            ->space(10)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('deftrench2')
            ->requires_local('deftrench')
            ->name('Wassergraben')
            ->global_blocking(true)
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Nun, da dein Graben voller Wasser ist, bist du praktisch vor Zombieangriffen geschützt - solange du dein Versteck nicht verlässt, versteht sich.')
            ->deco(20)
            ->energy(2)
            ->material([Model_Items_Generic_Waterv::cls() => 25])
            ->defense(100)
            ->effect(Model_Effect::factory()
                ->achieve(Model_Achievement::MA_LORD)
            )
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defimp')
            ->provide('impaler')
            ->global_blocking(true)
            ->name('Fallgruben')
            ->description('Ermöglicht es, einmal pro Belagerung eine große Menge Zombies zu vernichten. Muss nach dem Einsatz durch Verlassen des Verstecks reaktiviert werden.')
            ->message('Nichts ist befriedigender als das Geräusch von verfaulendem Fleisch, dass auf Holzpfähle gespießt wird!')
            ->energy(40)
            ->material([Model_Items_Generic_Wood::cls() => 2, Model_Items_Generic_Tube::cls() => 5, Model_Items_Generic_Sum::cls() => 1, Model_Items_Generic_Metal::cls() => 1])
            ->space(10)
    )

    //-- ++ STACK -> EPIC FOUNDATIONS / GARDEN
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('epc_garden');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden_floor')
            ->name('Boden aufreißen')
            ->description('Bevor du hier etwas pflanzen kannst, muss erstmal der Bodenbelag weg.')
            ->deco(-20)
            ->energy(50)
            ->produces([Model_Items_Generic_Wood::cls() => 6])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden_patch')
            ->requires('epc_garden_floor')
            ->name('Beet')
            ->description('Umgraben, abgrenzen, Hundehaufen platzieren - fertig!')
            ->deco(2)
            ->material([Model_Items_Generic_Wood::cls() => 4, Model_Items_Generic_Wire::cls() => 2])
            ->energy(20)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden_lights')
            ->name('Beleuchtung')
            ->description('Ohne ein bisschen Licht wird hier nichts wachsen...')
            ->deco(5)
            ->energy(10)
            ->material([Model_Items_Flashlight::cls() => 3, Model_Items_Generic_Wire::cls() => 1, Model_Items_Energy::cls() => 6])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden_water')
            ->name('Bewässerungssystem')
            ->description('Damit du auch etwas anderes ernten kannst als Staub.')
            ->energy(12)
            ->material([Model_Items_Generic_Tube::cls() => 4, Model_Items_Generic_Pressure::cls() => 1, Model_Items_Generic_Sum::cls() => 2])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden_final')
            ->requires_local('epc_garden_floor')->requires_local('epc_garden_patch')->requires_local('epc_garden_lights')->requires_local('epc_garden_water')
            ->produces([Model_Items_Virtual_Epic_Garden::cls() => 1])
            ->name('Abschließen: Kleines Gewächshaus')
            ->effect(Model_Effect::factory()->upgrade_achieve(Model_Achievement::MA_EPIC_BEGIN, Model_Achievement::MA_EPIC_END, 1, true, true))
    )

    // -- STACK -> EPIC FOUNDATIONS / GARDEN
    ->pop_stack()

    //++ STACK -> EPIC FOUNDATIONS / RAVEN
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('epc_raven');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven_hole')
            ->name('Wandöffnung')
            ->description('Dein Rabe kann keine Türen benutzen - wenn er hier rein- und rauskommen soll, musst du wohl oder übel ein kleines Loch in die Wand schlagen.')
            ->defense(-5)
            ->energy(10)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven_cage')
            ->name('Rabenkäfig')
            ->description('Baue deinem Raben lieber einen Käfig... sonst wirst du eines Nachts seine Krallen an deiner Kehle spüren.')
            ->deco(3)
            ->material([Model_Items_Generic_Sum::cls() => 4, Model_Items_Generic_Tube::cls() => 5, Model_Items_Generic_Cloth::cls() => 4])
            ->energy(10)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven_foodbin')
            ->name('Futterschale')
            ->description('Der Rabe kann sich entweder aus einer Futterschale oder deiner Leber bedienen... deine Entscheidung.')
            ->energy(5)
            ->material([Model_Items_Generic_Metal::cls() => 2, Model_Items_Generic_Wire::cls() => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven_lure')
            ->requires_local('epc_raven_cage')->requires('epc_raven_foodbin')
            ->name('Raben anlocken')
            ->description('Locke einen Raben an, damit du ihn trainieren kannst.')
            ->material([Model_Items_Rawmeat::cls() => 6, Model_Items_Basefood::cls() => 3])
    )


    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven_training')
            ->requires_local('epc_raven_lure')
            ->steps(3)
            ->name('Raben trainieren')
            ->description('Ist zumindest angenehmer, als einen bengalischen Tiger zu trainieren.')
            ->energy(60)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven_final')
            ->requires_local('epc_raven_hole')->requires_local('epc_raven_lure')->requires_local('epc_raven_cage')->requires_local('epc_raven_foodbin')->requires_local('epc_raven_training')
            ->produces([Model_Items_Virtual_Epic_Raven::cls() => 1])
            ->name('Abschließen: Raben-Bootcamp')
            ->effect(Model_Effect::factory()->upgrade_achieve(Model_Achievement::MA_EPIC_BEGIN, Model_Achievement::MA_EPIC_END, 1, true, true))
    )

    // -- STACK -> EPIC FOUNDATIONS / RAVEN
    ->pop_stack()

    //++ STACK -> EPIC FOUNDATIONS / FENCE
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('epc_fence');})


    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence_wiring')
            ->name('Verkabelungen')
            ->requires('gen1')
            ->description('So ein hochentwickelter Laserzaun muss korrekt verkabelt sein!')
            ->material([Model_Items_Generic_Wire::cls() => 3, Model_Items_Generic_Electro::cls() => 1])
            ->energy(15)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence_fusebox')
            ->name('Sicherungskasten')
            ->description('Der Sicherungskasten sorgt dafür, dass in deinem Versteck nicht jedes mal der Strom ausfällt, wenn ein Zombie in den Laserzaun läuft.')
            ->material([Model_Items_Generic_Wire::cls() => 1, Model_Items_Generic_Oven::cls() => 1, Model_Items_Shield2::cls() => 1])
            ->energy(15)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence_technobabble')
            ->name('Rückstrombeständiger Fluktuationskompensator mit vierfachen Elektronenfokus-Strahlern')
            ->description('Jedes Kind weis, dass man so etwas für einen Laserzaun benötigt!')
            ->material([Model_Items_Generic_Pressure::cls() => 1, Model_Items_Bone::cls() => 2, Model_Items_Dildo::cls() => 4, Model_Items_Generic_Boiler::cls() => 1])
            ->deco(5)
            ->energy(42)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence_lasers')
            ->name('Laser-Emittent')
            ->description('Vorsicht: Wiederholte Bestrahlung durch selbstgebaute Laser-Emittenten kann zur Ausbildung von Superkräften führen.')
            ->material([Model_Items_Generic_Lamp::cls() => 3, Model_Items_Flashlight::cls() => 3, Model_Items_Generic_Electro::cls() => 3, Model_Items_Generic_Ducttape::cls() => 1])
            ->energy(30)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence_final')
            ->requires_local('epc_fence_wiring')->requires_local('epc_fence_fusebox')->requires_local('epc_fence_technobabble')->requires_local('epc_fence_lasers')
            ->produces([Model_Items_Virtual_Epic_Fence::cls() => 1])
            ->name('Abschließen: Laserzaun')
            ->effect(Model_Effect::factory()->upgrade_achieve(Model_Achievement::MA_EPIC_BEGIN, Model_Achievement::MA_EPIC_END, 1, true, true))
    )

    // -- STACK -> EPIC FOUNDATIONS / FENCE
    ->pop_stack()
    ;