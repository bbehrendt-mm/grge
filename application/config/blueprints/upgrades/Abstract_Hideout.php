<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // External stuff
    ->add_blueprints(Model_Blueprint::factory()->id('hideout_slot')->name('Sicheres Versteck'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('outside')->name('Bebaubarer Aussenbereich'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('outside_space')->name('Großflächiger Aussenbereich'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('impaler')->name('Vorbereitete Fallgruben'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('slot_epic')->name('Bauplatz für epische Projekte'), true)

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier(Model_Blueprint::BP_MOD_ENERGY, function($pl,$pre,$e) { /** @var Model_Player $pl */
            $mod = 1;
            if (Tool_Scripts::get_timeofday($pl) == 'morning') $mod -= 0.25;    // Daytime bonus
            if ($pl->get_status()->retrieve('tr_handyman')) $mod -= 0.1;                     // Handyman Bonus
            return max(min(1,$e),floor($e*$mod));
        });
    })

    // Hideout repair stuff
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hideout')
            ->provide('hideout_slot')
            ->name('Versteck')
            ->description('Ermöglicht es dir, diesen Ort als Versteck zu nutzen.')
            ->message('Du hast so ziemlich alle Zugänge zu deinem Versteck notdürftig verbarrikadiert. Zwar ist das nur ein Anfang, aber vorerst solltest du hier sicher sein.')
            ->energy(20)
            ->material('Model_Items_Generic_Wood',2)
            ->decay(-100)
            ->category('Versteck')
    )

    // ++ STACK -> All blueprints below need the hideout
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('hideout');})

    // ++ STACK -> HIDEOUT category
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Versteck');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hideoutfx1')
            ->name('Notdürftige Reparatur')
            ->description('Repariert dein Versteck, beschleunigt jedoch auch dessen Verfall.')
            ->message('Löcher im Boden und in den Wänden? Nicht isolierte Starkstromkabel, die von der Decke hängen? Ein in Flammen stehendes Bett? Kein Problem für einen Handwerkermeister wie dich! Im Nuh ist alles notdürftig und mit minderwertigen Werkzeugen und Rohstoffen geflickt. Das wird zwar nicht allzu lange halten, aber für die nächsten paar Sekunden ist die Einsturzgefahr deines Verstecks gebannt.')
            ->steps(0)
            ->energy(20)
            ->material('Model_Items_Generic_Crwood',3)->material('Model_Items_Generic_Crmetal',3)
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
            ->material('Model_Items_Generic_Wood',3)->material('Model_Items_Generic_Metal',3)
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
            ->material(['Model_Items_Generic_Wood' => 1, 'Model_Items_Generic_Metal' => 1, 'Model_Items_Generic_Sum' => 3])
            ->decay(0, -0.10)
    )

    // -- ++ STACK -> BEDROOM category
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Schlafzimmer');})


    // Bedroom
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedrwake')
            ->name('Alarmdraht')
            ->description('Weckt alle schlafenden Spieler beim Eindringen von Zombies. Wird beim Einsatz zerstört.')
            ->message('Dieser Alarmdraht macht ordentlich Lärm, wenn Zombies im Begriff sind, dein Versteck zu attackieren. Von jetzt an brauchst du keine Angst mehr zu haben, deinen eigenen Tod zu verschlafen.')
            ->energy(1)
            ->material(['Model_Items_Generic_Wire' => 1, 'Model_Items_Generic_Crmetal' => 2])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedrlights')
            ->requires('bedr1')
            ->name('Nachtlicht')
            ->description('Reduziert die Einschlafzeit.')
            ->message('Endlich brauchst du dich im Dunklen nicht mehr zu fürchten - dieses neue Nachtlicht hilft dir beim Einschlafen und vertreibt schlimme Träume.')
            ->deco(2)
            ->energy(1)
            ->material(['Model_Items_Generic_Lamp' => 1, 'Model_Items_Energy' => 1])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedr1')
            ->name('Schlafecke')
            ->description('Verbessert Regeneration von Energie und Müdigkeit beim Schlafen. Ermöglicht außerdem die Regeneration von Gesundheit beim Schlafen.')
            ->message('Endlich musst du nicht mehr auf dem Boden schlafen - mit diesem neuen Bett hat dein Versteck nun endlich die Behaglichkeit einer simplen Crackhütte gewonnen!')
            ->deco(2)
            ->energy(5)
            ->material('Model_Items_Generic_Bed',1)
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedr2')
            ->requires('bedr1')
            ->name('Kuschelige Schlafecke')
            ->description('Verbessert Regeneration von Energie, Müdigkeit und Gesundheit beim Schlafen.')
            ->message('Mit einer Decke und einem Teddy ist dein Bett gleich viel kuscheliger. Deine Schlafecke sieht jetzt schon richtig gemütlich aus!')
            ->deco(1)
            ->energy(5)
            ->material(['Model_Items_Generic_Teddy' => 1, 'Model_Items_Generic_Cloth' => 3])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedr3')
            ->requires('bedr2')
            ->name('Kingsize-Bett')
            ->description('Verbessert Regeneration von Energie, Müdigkeit und Gesundheit beim Schlafen.')
            ->message('Dank deinem neuen KingSize-Bett hast du nun extra viel Platz, dich Nachts vor Angst in deinem Bett herumzuwälzen.')
            ->deco(1)
            ->energy(5)
            ->material(['Model_Items_Generic_Bed' => 1, 'Model_Items_Generic_Cloth' => 3])
    )

    // -- ++ STACK -> SITTING category
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Sitzecke');})

    // Sitting area
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('sofa1')
            ->name('Sitzecke')
            ->description('Erhöht bei Benutzung die Energieregeneration. Der Effekt verstärkt sich, wenn die Sitzecke von mehreren Spielern verwendet wird.')
            ->message('Mit dieser Sitzecke kannst du dich nun endlich vernünftig entspannen ohne dich immer gleich ins Bett legen zu müssen.')
            ->deco(2)
            ->energy(3)
            ->material(['Model_Items_Abstract_Chair' => 3])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('sofa2')
            ->requires('sofa1')
            ->name('Sesselecke')
            ->description('Verbessert die Regenerationswirkung der Sitzecke. Verstärkt außerdem die Effekte beim Lesen von Büchern.')
            ->message('Diese Sessel sehen ein wenig... eigenwillig aus. Aber zumindest sind sie bequem - mehr kann man doch nun wirklich nicht verlangen. Wobei... ein Getränkehalter wäre natürlich schön...')
            ->deco(1)
            ->energy(25)
            ->material(['Model_Items_Abstract_Chair' => 1, 'Model_Items_Generic_Bed' => 1, 'Model_Items_Generic_Cloth' => 4])
    )

    // -- ++ STACK -> WORKBENCH category
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Werkbank');})

    // Workbench
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('manu1')
            ->name('Werkbank')
            ->description('Ermöglicht die Herstellung verschiedener Gegenstände.')
            ->message('Ein Mann ohne Werkbank ist einfach kein richtiger Mann! (Eine Frau ohne Werkbank ist natürlich auch kein richtiger Mann.) Jetzt kannst du endlich viel Geld ausgeben und Zeug bauen, dass viel weniger kosten würde wenn du es einfach fertig kaufen würdest. Hurra!')
            ->energy(10)
            ->material(['Model_Items_Generic_Table' => 1, 'Model_Items_Abstract_Chair' => 1])
    )

    // ++ STACK -> All blueprints below need the workbench
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('manu1');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('manu2')
            ->name('Stromversorgung an der Werkbank')
            ->description('Schaltet zusätzliche Optionen für die Werkbank frei.')
            ->message('Ohne das Risiko tödlicher Stromschläge macht die Arbeit einfach keinen Spaß! Darum sind offene Drähte ohne Sicherung einfach ein Muss für jede Werkbank!')
            ->energy(10)
            ->material(['Model_Items_Generic_Lamp' => 1, 'Model_Items_Generic_Electro' => 3, 'Model_Items_Energy' => 5])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('manuspd')
            ->name('Werkbank-Halterungen')
            ->description('Reduziert die benötigte Energie für alle Arbeiten an der Werkbank.')
            ->message('Diese neuen Halterungen werden sich sicher als nützlich erweisen, wenn es mal etwas schweres zu heben gibt. Hoffentlich hast du beim bau nicht gepfuscht, sonst werden sie sich zusätzlich noch als tödlich erweisen...')
            ->energy(15)
            ->material(['Model_Items_Generic_Wood' => 3, 'Model_Items_Generic_Sum' => 1])
    )

    // -- STACK -> All blueprints below NO LONGER need the workbench
    ->pop_stack()

    // -- ++ STACK -> GENERATOR category
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Generator');})

    // Generator
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('gen1')
            ->name('Notstrom-Aggregat')
            ->description('Ermöglicht es, Strom aus Batterien zu gewinnen.')
            ->message('Endlich verfügst du über ein Notstrom-Aggregat, jetzt musst du nicht mehr im Dunkeln fernsehen! Yuhuu!')
            ->deco(-5)
            ->energy(15)
            ->material(['Model_Items_Generic_Electro' => 2, 'Model_Items_Generic_Metal' => 5, 'Model_Items_Generic_Tube' => 1])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('gen2')
            ->requires('gen1')
            ->name('Diesel-Generator')
            ->description('Ermöglicht es, Strom aus Energie und Benzinkanistern zu gewinnen.')
            ->message('Alle paar Minuten neue Batterien einzulegen kann schon nerven. Glücklicherweise kannst du diesem Problem mit einem Kanister Benzin vorsorgen. Einziger Haken: Du brauchst einen Kanister Benzin ...')
            ->deco(-5)
            ->energy(15)
            ->material(['Model_Items_Generic_Motor' => 1, 'Model_Items_Generic_Sum' => 5, 'Model_Items_Generic_Metal' => 2])
    )

    // -- ++ STACK -> KITCHEN category
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Küche');})

    // Kitchen
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc1')
            ->name('Küchentisch')
            ->description('Ermöglicht die Herstellung verschiedener Speisen.')
            ->message('Stolz stehst du vor deinem neuen Küchentisch; endlose kulinarische Möglichkeiten tauchen vor deinem geistigen Auge auf, verfliegen allerdings schnell wieder als dir einfällt, dass du für endlose kulinarische Möglichkeiten auch kulinarische Zutaten benötigst. Naja.... so eine verrottete Leiche lässt sich bestimmt auch fantasievoll zubereiten.')
            ->energy(5)
            ->material(['Model_Items_Generic_Table' => 1])
    )

    // ++ STACK -> All blueprints below need the kitchen
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('ktc1');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc2')
            ->name('Wasserkocher')
            ->description('Schaltet zusätzliche Optionen in der Küche frei.')
            ->message('Das zentrale Utensil jeder Küche - der Wasserkocher - steht nun auch dir zur Verfügung. Nutze ihn Weise, und missbrauche seine Kräfte nicht!')
            ->energy(2)
            ->material(['Model_Items_Generic_Boiler' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc3')
            ->name('Küchenutensilien')
            ->description('Schaltet zusätzliche Optionen in der Küche frei.')
            ->message('ENDLICH! Nun musst du den Brei nicht mehr mit der Hand kneten und das Fleisch nicht mehr mit Karateschlägen schneiden. Heureka!')
            ->material(['Model_Items_Generic_Mixer' => 1, 'Model_Items_Knife' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('ktc4')
            ->name('Ofen')
            ->description('Schaltet zusätzliche Optionen in der Küche frei.')
            ->message('Die Zeiten von eiskaltem Essen sind vorbei! Vorrausgesetzt natürlich, du kannst etwas Strom auftreiben ...')
            ->energy(20)
            ->material(['Model_Items_Generic_Oven' => 1])
    )

    // -- STACK -> All blueprints below NO LONGER need the kitchen
    ->pop_stack()

    // -- ++ STACK -> DEFENSE category
    ->pop_stack()->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Verteidigung');})

    // Defense
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall1')
            ->name('Barrikade (Tür)')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #1 gegen Zombieinvasionen: Mach die Tür zu!')
            ->deco(-5)
            ->energy(5)
            ->material(['Model_Items_Generic_Wood' => 1])
            ->defense(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall2')
            ->requires('defwall1')
            ->name('Barrikade (Fenster)')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #2 gegen Zombieinvasionen: Eine geschlossene Tür hilft nicht viel, wenn die Fenster noch sperrangelweit offen stehen!')
            ->deco(-7)
            ->steps(3)
            ->energy(10)
            ->material(['Model_Items_Generic_Wood' => 2])
            ->defense(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall3')
            ->requires('defwall2')
            ->name('Barrikade (Wände)')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #3 gegen Zombieinvasionen: Wenn Zombies keine Löcher in deiner Verteidigung finden, dann machen sie sich selbst welche! Verstärke also besser immer deine Wände.')
            ->deco(-3)
            ->steps(4)
            ->energy(15)
            ->material(['Model_Items_Generic_Wood' => 2, 'Model_Items_Generic_Metal' => 1])
            ->defense(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall4')
            ->requires('defwall3')
            ->name('Barrikade (Dach)')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #4 gegen Zombieinvasionen: Wenn Zombies nicht von links, rechts, vorne und hinten kommen können, dann kommen sie eben von oben!')
            ->deco(-2)
            ->steps(3)
            ->energy(25)
            ->material(['Model_Items_Generic_Wood' => 2, 'Model_Items_Generic_Metal' => 2, 'Model_Items_Generic_Sum' => 2])
            ->defense(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall5')
            ->requires('defwall4')
            ->name('Barrikaden')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #4 gegen Zombieinvasionen: Wenn Zombies nicht von links, rechts, vorne und hinten kommen können, dann kommen sie eben von oben!')
            ->deco(-10)
            ->steps(0)
            ->energy(50)
            ->material(['Model_Items_Generic_Metal' => 4,'Model_Items_Generic_Table' => 2, 'Model_Items_Generic_Ducttape' => 4])
            ->defense(5)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('deffence1')
            ->requires('outside')
            ->provide('fence')
            ->name('Holzzaun')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Nichts hält Zombies eher ab als ein weißer, frisch lackierter Holzzaun! Glaubst du nicht? Probier es halt aus!')
            ->deco(10)
            ->energy(30)
            ->material(['Model_Items_Generic_Wood' => 10, 'Model_Items_Generic_Ducttape' => 2])
            ->defense(10)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('deffence2')
            ->requires('outside')
            ->provide('fence')
            ->name('Makaberer Zaun')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Was tun, wenn sich die Knochen im Lagerraum langsam stapeln? Bau einen Zaun damit! Das ist eine gute Beschäftigungstherapie und sieht einfach megacool aus. Achja, Zombies kannst du auf die Art auch von deinem Rasen fernhalten.')
            ->deco(-5)
            ->energy(30)
            ->material(['Model_Items_Bone' => 20, 'Model_Items_Generic_Ducttape' => 4])
            ->defense(10)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('deftrench')
            ->requires('outside')
            ->requires('outside_space')
            ->name('Graben')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Puuh, endlich fertig. Es war ein hartes Stück Arbeit, aber du hast es geschafft einen mehrere Meter tiefen Graben um das Versteck zu schaufeln. Den werden die Zombies niemals überwinden können - es sei denn, es fallen genug Zombies hinein, dass die restlichen einfach drüberlaufen können...')
            ->energy(95)
            ->defense(15)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('deftrench2')
            ->requires('deftrench')
            ->name('Wassergraben')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Nun, da dein Graben voller Wasser ist, bist du praktisch vor Zombieangriffen geschützt - solange du dein Versteck nicht verlässt, versteht sich.')
            ->deco(20)
            ->energy(2)
            ->material(['Model_Items_Generic_Waterv' => 25])
            ->defense(100)
            ->effect(Model_Effect::factory()
                ->achieve(Model_Achievement::MA_LORD)
            )
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defimp')
            ->requires('outside')
            ->provide('impaler')
            ->name('Fallgruben')
            ->description('Ermöglicht es, einmal pro Belagerung eine große Menge Zombies zu vernichten. Muss nach dem Einsatz durch Verlassen des verstecks reaktiviert werden.')
            ->message('Nichts ist befriedigender als das Geräusch von verfaulendem Fleisch, dass auf Holzpfähle gespießt wird!')
            ->energy(40)
            ->material(['Model_Items_Generic_Wood' => 2, 'Model_Items_Generic_Tube' => 5, 'Model_Items_Generic_Sum' => 1, 'Model_Items_Generic_Metal' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defbat')
            ->name('Stationäres Batteriegeschütz')
            ->description('Ermöglicht es, bei einer Belagerung Zombies durch Batterien zu töten.')
            ->message('Say hello to my little friend!')
            ->energy(20)
            ->material(['Model_Items_Generic_Sum' => 1, 'Model_Items_Generic_Tube' => 2, 'Model_Items_Generic_Metal' => 1])

    )

    // -- STACK -> Categories
    ->pop_stack()

    // ++ STACK -> DECO category
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->category('Dekoration');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bkcase')
            ->name('Bücherregal')
            ->description('Kann mit Büchern gefüllt werden, um den Dekorationswert des Verstecks zu verbessern.')
            ->energy(15)
            ->deco(2)
            ->material(['Model_Items_Generic_Wood' => 6])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bkcase_use')
            ->name('Buch einlagern')
            ->requires('bkcase')
            ->steps(20)
            ->description('Legt ein Buch ins Bücherregal.')
            ->deco(4)
            ->material(['Model_Items_Book' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('curtains')
            ->name('Vorhänge')
            ->steps(3)
            ->description('Stattet dein Versteck mit hübschen Vorhängen aus und verbessert so den Dekorationswert.')
            ->deco(5)
            ->material(['Model_Items_Generic_Cloth' => 4])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bottlecol')
            ->name('Flaschensammlung')
            ->description('Nichts schmückt eine Wohnung mehr als ein riesiger Haufen leerer Bierflaschen.')
            ->deco(10)
            ->material('Model_Items_Smallbottle', 6, function($i) {/** @var Model_Items_Smallbottle $i */ return $i->count() == 0;})
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('garland_ca')
            ->provide('garland')
            ->name('Kapitalistische Girlande')
            ->description('Stelle deinen Reichtum mit dieser dekorativen Girlande zur Schau.')
            ->deco(15)
            ->material(['Model_Items_Money' => 5, 'Model_Items_Generic_Wire' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('garland_ma')
            ->provide('garland')
            ->name('Makabere Girlande')
            ->description('Es gibt nichts, aus dem man besser eine dekorative Girlande bauen kann als abgenagte Knochen! ... moment ...')
            ->deco(15)
            ->material(['Model_Items_Bone' => 5, 'Model_Items_Generic_Wire' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('garland_co')
            ->provide('garland')
            ->name('Elektrisierende Girlande')
            ->description('Diese hübsch glitzernde Girlande wertet dein Versteck dekorativ auf. Pass nur auf, dass dir keine Batteriesäure auf den Kopf tropft...')
            ->deco(15)
            ->material(['Model_Items_Battery' => 15, 'Model_Items_Generic_Wire' => 1])
    )

    // -- STACK -> Categories
    ->pop_stack()

    //++ STACK -> EPIC FOUNDATIONS
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->provide('epic')->requires('slot_epic')->category('Epische Projekte')->confirm('Bist du sicher, dass du die Arbeit an dem epischen Projekt ":name" beginnen möchtest? Denk daran, dass du nur ein episches Projekt pro Versteck errichten kannst!')->message('Du hast die Arbeiten an einem epischen Projekt in deinem Versteck begonnen. Viel Erfolg!')->effect(Model_Effect::factory()->achieve(Model_Achievement::MA_EPIC_BEGIN, 1, true));})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden')
            ->name('Kleines Gewächshaus')
            ->description('Wie Millionen von Pot-Farmern vor dir kannst auch du mit diesem patentierten Gewächshaus-Bausatz deinen grünen Daumen entdecken und verschiedene nützliche Gewächse anpflanzen. Aber Achtung: Ein solcher Garten benötigt viel Aufmerksamkeit und Zeit, bevor du etwas ernten kannst!')
    )

    /*->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_drill')
            ->name('Grundwasserversorgung')
            ->description('Warum gammliges Kondenswasser von alten Bahnhofstoiletten ablecken, wenn du dir frisches Wasser aus dem Boden besorgen kannst? Zwar wird der Bau dieses Projektes dich sehr viel Energie kosten, dafür verfügst du danach über eine (zumindest halbwegs) stetige Wasserversorgung.')
    )*/

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven')
            ->name('Raben-Bootcamp')
            ->description('Raben sind intelligente (und boshafte) Tiere - aber mit ein bisschen Geschick könntest du sie vielleicht dazu trainieren, für dich nach Gegenständen zu suchen. Du müsstest sie dafür natürlich mit etwas Futter belohnen...')
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence')
            ->name('Laserzaun')
            ->description('Zombies sind nicht gerade für ihre Geschicklichkeit bekannt - daher kannst du sie mit ein paar Laserbarrieren bestimmt recht zuverlässig von deinem Versteck fernhalten. Vorrausgesetzt natürlich, dir gehen nicht die Batterien aus...')
    )

    // -- STACK -> Categories
    ->pop_stack()

    //++ STACK -> EPIC FOUNDATIONS / GARDEN
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('epc_garden')->category('Epische Projekte: Kleines Gewächshaus');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden_floor')
            ->name('Boden aufreißen')
            ->description('Bevor du hier etwas pflanzen kannst, muss erstmal der Bodenbelag weg.')
            ->deco(-20)
            ->energy(50)
            ->produces(['Model_Items_Generic_Wood' => 6])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden_patch')
            ->requires('epc_garden_floor')
            ->name('Beet')
            ->description('Umgraben, abgrenzen, Hundehaufen platzieren - fertig!')
            ->deco(2)
            ->material(['Model_Items_Generic_Wood' => 4, 'Model_Items_Generic_Wire' => 2])
            ->energy(20)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden_lights')
            ->name('Beleuchtung')
            ->description('Ohne ein bisschen Licht wird hier nichts wachsen...')
            ->deco(5)
            ->energy(10)
            ->material(['Model_Items_Flashlight' => 3, 'Model_Items_Generic_Wire' => 1, 'Model_Items_Energy' => 6])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden_water')
            ->name('Bewässerungssystem')
            ->description('Damit du auch etwas anderes ernten kannst als Staub.')
            ->energy(12)
            ->material(['Model_Items_Generic_Tube' => 4, 'Model_Items_Generic_Pressure' => 1, 'Model_Items_Generic_Sum' => 2])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_garden_final')
            ->requires('epc_garden_floor')->requires('epc_garden_patch')->requires('epc_garden_lights')->requires('epc_garden_water')
            ->produces(['Model_Items_Virtual_Epic_Garden' => 1])
            ->name('Abschließen: Kleines Gewächshaus')
            ->effect(Model_Effect::factory()->upgrade_achieve(Model_Achievement::MA_EPIC_BEGIN, Model_Achievement::MA_EPIC_END, 1, true, true))
    )

    // -- STACK -> EPIC FOUNDATIONS / GARDEN
    ->pop_stack()

    //++ STACK -> EPIC FOUNDATIONS / RAVEN
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('epc_raven')->category('Epische Projekte: Raben-Bootcamp');})

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
            ->material(['Model_Items_Generic_Sum' => 4, 'Model_Items_Generic_Tube' => 5, 'Model_Items_Generic_Cloth' => 4])
            ->energy(10)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven_foodbin')
            ->name('Futterschale')
            ->description('Der Rabe kann sich entweder aus einer Futterschale oder deiner Leber bedienen... deine Entscheidung.')
            ->energy(5)
            ->material(['Model_Items_Generic_Metal' => 2, 'Model_Items_Generic_Wire' => 1])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven_lure')
            ->requires('epc_raven_cage')->requires('epc_raven_foodbin')
            ->name('Raben anlocken')
            ->description('Locke einen Raben an, damit du ihn trainieren kannst.')
            ->material(['Model_Items_Rawmeat' => 6, 'Model_Items_Basefood' => 3])
    )


    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven_training')
            ->requires('epc_raven_lure')
            ->steps(3)
            ->name('Raben trainieren')
            ->description('Ist zumindest angenehmer, als einen bengalischen Tiger zu trainieren.')
            ->energy(60)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_raven_final')
            ->requires('epc_raven_hole')->requires('epc_raven_lure')->requires('epc_raven_cage')->requires('epc_raven_foodbin')->requires('epc_raven_training')
            ->produces(['Model_Items_Virtual_Epic_Raven' => 1])
            ->name('Abschließen: Raben-Bootcamp')
            ->effect(Model_Effect::factory()->upgrade_achieve(Model_Achievement::MA_EPIC_BEGIN, Model_Achievement::MA_EPIC_END, 1, true, true))
    )

    // -- STACK -> EPIC FOUNDATIONS / RAVEN
    ->pop_stack()

    //++ STACK -> EPIC FOUNDATIONS / FENCE
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('epc_fence')->category('Epische Projekte: Laserzaun');})


    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence_wiring')
            ->name('Verkabelungen')
            ->requires('gen1')
            ->description('So ein hochentwickelter Laserzaun muss korrekt verkabelt sein!')
            ->material(['Model_Items_Generic_Wire' => 8])
            ->energy(15)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence_fusebox')
            ->name('Sicherungskasten')
            ->description('Der Sicherungskasten sorgt dafür, dass in deinem Versteck nicht jedes mal der Strom ausfällt, wenn ein Zombie in den Laserzaun läuft.')
            ->material(['Model_Items_Generic_Wire' => 3, 'Model_Items_Generic_Oven' => 1, 'Model_Items_Shield2' => 1])
            ->energy(15)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence_technobabble')
            ->name('Rückstrombeständiger Fluktuationskompensator mit vierfachen Elektronenfokus-Strahlern')
            ->description('Jedes Kind weis, dass man so etwas für einen Laserzaun benötigt!')
            ->material(['Model_Items_Generic_Pressure' => 1, 'Model_Items_Bone' => 2, 'Model_Items_Dildo' => 4, 'Model_Items_Generic_Boiler' => 1])
            ->deco(5)
            ->energy(42)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence_lasers')
            ->name('Laser-Emittent')
            ->description('Vorsicht: Wiederholte Bestrahlung durch selbstgebaute Laser-Emittenten kann zur Ausbildung von Superkräften führen.')
            ->material(['Model_Items_Generic_Lamp' => 3, 'Model_Items_Flashlight' => 3, 'Model_Items_Generic_Electro' => 3, 'Model_Items_Generic_Ducttape' => 1])
            ->energy(30)
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('epc_fence_final')
            ->requires('epc_fence_wiring')->requires('epc_fence_fusebox')->requires('epc_fence_technobabble')->requires('epc_fence_lasers')
            ->produces(['Model_Items_Virtual_Epic_Fence' => 1])
            ->name('Abschließen: Laserzaun')
            ->effect(Model_Effect::factory()->upgrade_achieve(Model_Achievement::MA_EPIC_BEGIN, Model_Achievement::MA_EPIC_END, 1, true, true))
    )

    // -- STACK -> EPIC FOUNDATIONS / FENCE
    ->pop_stack()


    ->validate();