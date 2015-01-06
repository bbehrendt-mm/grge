<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    // Hideout repair stuff
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hideout')
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
            ->decay(-10, 0,05)
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
            ->name('Diesel-Generator')
            ->description('Ermöglicht es, Strom aus Energie und Benzinkanistern zu gewinnen.')
            ->message('Alle paar Minuten neue Batterien einzulegen kann schon nerven. Glücklicherweise kannst du diesem Problem mit einem Kanister Benzin vorsorgen. Einziger Haken: Du brauchst einen Kanister Benzin ...')
            ->energy(15)
            ->material(['Model_Items_Generic_Motor' => 1, 'Model_Items_Generic_Sum' => 5, 'Model_Items_Generic_Metal' => 2])
    )

    ->validate();