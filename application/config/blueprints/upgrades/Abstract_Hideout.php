<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // External stuff
    ->add_blueprints(Model_Blueprint::factory()->id('outside')->name('Bebaubarer Aussenbereich'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('outside_space')->name('Großflächiger Aussenbereich'), true)
    ->add_blueprints(Model_Blueprint::factory()->id('impaler')->name('Vorbereitete Fallgruben'), true)

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
    )

    // ++ STACK -> All blueprints below need the hideout
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires('hideout');})

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
            ->energy(1)
            ->material(['Model_Items_Generic_Lamp' => 1, 'Model_Items_Energy' => 1])
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedr1')
            ->name('Schlafecke')
            ->description('Verbessert Regeneration von Energie und Müdigkeit beim Schlafen. Ermöglicht außerdem die Regeneration von Gesundheit beim Schlafen.')
            ->message('Endlich musst du nicht mehr auf dem Boden schlafen - mit diesem neuen Bett hat dein Versteck nun endlich die Behaglichkeit einer simplen Crackhütte gewonnen!')
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
            ->energy(5)
            ->material(['Model_Items_Generic_Bed' => 1, 'Model_Items_Generic_Cloth' => 3])
    )

    // Sitting area
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('sofa1')
            ->name('Sitzecke')
            ->description('Erhöht bei Benutzung die Energieregeneration. Der Effekt verstärkt sich, wenn die Sitzecke von mehreren Spielern verwendet wird.')
            ->message('Mit dieser Sitzecke kannst du dich nun endlich vernünftig entspannen ohne dich immer gleich ins Bett legen zu müssen.')
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
            ->energy(25)
            ->material(['Model_Items_Abstract_Chair' => 1, 'Model_Items_Generic_Bed' => 1, 'Model_Items_Generic_Cloth' => 4])
    )

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

    // Generator
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('gen1')
            ->name('Notstrom-Aggregat')
            ->description('Ermöglicht es, Strom aus Batterien zu gewinnen.')
            ->message('Endlich verfügst du über ein Notstrom-Aggregat, jetzt musst du nicht mehr im Dunkeln fernsehen! Yuhuu!')
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
            ->energy(15)
            ->material(['Model_Items_Generic_Motor' => 1, 'Model_Items_Generic_Sum' => 5, 'Model_Items_Generic_Metal' => 2])
    )

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

    // Defense

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('defwall1')
            ->name('Barrikade (Tür)')
            ->description('Verstärkt die Verteidigung des Verstecks.')
            ->message('Überlebenstipp #1 gegen Zombieinvasionen: Mach die Tür zu!')
            ->steps(3)
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
            ->steps(3)
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



    ->validate();