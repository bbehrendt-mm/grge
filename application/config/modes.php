<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
    'modes' => array(

        0 => array(
            'type' => 'none',
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),
            'jobs' => array(),
            'unstartable_jobs' => array(),
            'setup' => array(
                'inherit' => array(),
                'config' => array(
                    'zombies.accum'                         => 1,
                    'zombies.cowardly'                      => false,
                    'zombies.escape_threshold'              => 10,
                    'places.dryout_factor'                  => 1,
                    'places.outworld.spawn_stranger'        => false,
                    'places.outworld.alt_spawn_stranger'    => false,
                    'places.outworld.location_density'      => 1,
                    'items.bottle.allow_full_detox'         => true,
                    'items.water.tox_dirty'                 => 8,
                    'items.water.tox_polluted'              => 15,
                    'items.ammobelt.startup_bat.min'        => 10,
                    'items.ammobelt.startup_bat.max'        => 20,
                    'items.ammobelt.startup_blt.min'        => 0,
                    'items.ammobelt.startup_blt.max'        => 3,
                    'items.pill.use_default_effect_proc'    => true,
                    'modules.mapping'                       => false,
                    'modules.armory'                        => true,
                    'modules.additionalchems'               => true,
                    'modules.multiplayer'                   => false,
                    'game.bhav.infections'                  => false,
                    'game.config.map'                       => 'default',
                    'game.config.itemset'                   => 'default',
                    'game.config.spawn'                     => 'default'
                ),
                'spawn' => array(),
            ),
        ),
        
        
        1000 => array(
            'meta' => array(
                'name' => 'Survival',
                'caption' => 'Ziel: Überleben! Mal sehen wie lange das gut geht.',
                'headline' => 'Der Klassiker',
                'body' => 'Bleib am leben solange du kannst, um Punkte zu erhalten.'
            ),
            'type' => 'single',
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),
            'jobs' => array(1011, 1012, 1020, 1030, 1040, 1050, 1060, 1070),
            'unstartable_jobs' => array(),
            'setup' => array(
                'inherit' => array(0),
                'config' => array(
                    'places.outworld.spawn_stranger'        => true,

                    'ranking.points.zombie_kills.factor'    => 0,
                    'ranking.points.zombie_kills.offset'    => 0,
                    'ranking.points.survival.factor'        => 1,
                    'ranking.points.survival.offset'        => 0,
                    'ranking.points.home.factor'            => 0,
                    'ranking.points.home.offset'            => 0,
                    'ranking.points.home.stretch'           => 1,
                    'ranking.points.home.threshold'         => 0,
                ),
                'spawn' => array(),
            ),
        ),
        1100 => array(
            'meta' => array(
                'name' => 'Hardcore',
                'caption' => 'Vergiss den Zombie-Ponyhof - hier gehts richtig zur Sache!',
                'headline' => 'Der Klassiker - Extra-Würzig',
                'body' => 'Wie lange kannst du überleben, wenn dich dein Glück verlassen hat?'
            ),
            'type' => 'single',
            'requirements' => array(
                'mode' => array(1000 => 100),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'jobs' => array(1020, 1030, 1040, 1050, 1060, 1070),
            'unstartable_jobs' => array(),

            'setup' => array(
                'inherit' => array(1000),
                'config' => array(
                    'zombies.escape_threshold'              => 20,
                    'places.outworld.spawn_stranger'        => true,
                    'places.outworld.location_density'      => 0.3,
                    'items.water.tox_dirty'                 => 15,
                    'items.water.tox_polluted'              => 45,
                    'places.dryout_factor'                  => 2,
                    'items.pill.use_default_effect_proc'    => false,
                    'items.bottle.allow_full_detox'         => false,

                    'ranking.points.survival.factor'        => 1.5,
                ),
                'spawn' => array(),
            ),
        ),
        2000 => array(
            'meta' => array(
                'name' => 'Zombie-Massaker',
                'caption' => 'Schnauze voll von Zombies? Dann bist du hier richtig!	',
                'headline' => 'Einmal Gemetzel, bitte!',
                'body' => 'Versuche so viele Zombies wie möglich in einer Spielwoche platt zu machen. Stirbst du vor Ablauf der Woche gibt\'s Punktabzug'
            ),
            'type' => 'single',
            'requirements' => array(
                'mode' => array(1000 => 100),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'jobs' => array(2010, 2020, 2030),
            'unstartable_jobs' => array(),

            'setup' => array(
                'inherit' => array(0),
                'config' => array(
                    'zombies.escape_threshold'          => 0,
                    'zombies.cowardly'                  => true,
                    'items.ammobelt.startup_bat.min'    => 150,
                    'items.ammobelt.startup_bat.max'    => 150,
                    'items.ammobelt.startup_blt.min'    => 65,
                    'items.ammobelt.startup_blt.max'    => 65,
                     
                    'ranking.points.zombie_kills.factor'=> 1,
                    'ranking.points.zombie_kills.offset'=> 0,
                    'ranking.points.survival.factor'    => 0,
                    'ranking.points.survival.offset'    => -91,
                    'ranking.points.home.factor'        => 0,
                    'ranking.points.home.offset'        => 0,
                    'ranking.points.home.stretch'       => 1,
                    'ranking.points.home.threshold'     => 0,
                ),
                'spawn' => array(),
            ),
        ),
        3000 => array(
            'meta' => array(
                'name' => 'Letzter Aufklärer',
                'caption' => 'Du liebst das Risiko? Dann immer rein mit dir!',
                'headline' => 'Kann ich bitte die Karte haben?',
                'body' => 'Du bist der letzte Aufklärer deiner Stadt und wurdest auf ein Himmelfahrtskommando geschickt, um die Umgebung zu kartographieren.'
            ),
            'type' => 'single',
            'requirements' => array(
                'mode' => array(1000 => 500, 1100 => 250),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'jobs' => array(3010, 3020, 3030),
            'unstartable_jobs' => array(),

            'setup' => array(
                'inherit' => array(0),
                'config' => array(
                    'modules.mapping'                   => true,

                    'ranking.points.zombie_kills.factor'=> 0,
                    'ranking.points.zombie_kills.offset'=> 0,
                    'ranking.points.survival.factor'    => 0,
                    'ranking.points.survival.offset'    => 0,
                    'ranking.points.home.factor'        => 1,
                    'ranking.points.home.offset'        => 0,
                    'ranking.points.home.stretch'       => 1000,
                    'ranking.points.home.threshold'     => -50,
                ),
                'spawn' => array(),
            ),
        ),
        4000 => array(
            'meta' => array(
                'name' => 'Kolosseum',
                'caption' => 'Du vs. Zombies! Das ist das ultimative Kräftemessen!',
                'headline' => 'Frisch aus Rom',
                'body' => 'Wie lange kannst du im postapokalyptischen Kolosseum überleben? Wie viele Wellen von Zombies wirst du aushalten? Es gibt nur einen Weg, das herauszufinden...'
            ),
            'type' => 'single',
            'requirements' => array(
                'mode' => array(1000 => 500, 1100 => 250),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'jobs' => array(4010),
            'unstartable_jobs' => array(),

            'setup' => array(
                'inherit' => array(0),
                'config' => array(
                    'modules.armory'                        => true,

                    'ranking.points.zombie_kills.factor'    => 0,
                    'ranking.points.zombie_kills.offset'    => 0,
                    'ranking.points.survival.factor'        => 0,
                    'ranking.points.survival.offset'        => 0,
                    'ranking.points.home.factor'            => 1,
                    'ranking.points.home.offset'            => 0,
                    'ranking.points.home.stretch'           => 2,
                    'ranking.points.home.threshold'         => 1,
                ),
                'spawn' => array(),
            ),
        ),
        10000 => array(
            'meta' => array(
                'name' => 'Gruppen-Survival',
                'caption' => '',
                'headline' => '',
                'body' => ''
            ),
            'type' => 'multi_auto',
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),

            'jobs' => array(10010,10020,10030,10040,1080),
            'unstartable_jobs' => array(),

            'setup' => array(
                'inherit' => array(0),
                'config' => array(
                    'places.outworld.spawn_stranger'    =>  false,
                    'places.outworld.alt_spawn_stranger'=>  true,

                    'ranking.points.zombie_kills.factor'=>  0,
                    'ranking.points.zombie_kills.offset'=>  0,
                    'ranking.points.survival.factor'    =>  1,
                    'ranking.points.survival.offset'    =>  0,
                    'ranking.points.home.factor'        =>  0,
                    'ranking.points.home.offset'        =>  0,
                    'ranking.points.home.stretch'       =>  1,
                    'ranking.points.home.threshold'     =>  0,

                    'modules.multiplayer'               =>  true,
                    'game.bhav.infections'              =>  true,
                ),
                'spawn' => array(),
            ),
        ),
        10100 => array(
            'meta' => array(
                'name' => 'Survival Privat',
                'caption' => 'In einer gemütlichen Runde gemeinsam sterben.',
                'headline' => 'Haben Sie eine Reservierung?',
                'body' => 'In diesem Modus kannst du mit deinen Freunden gemeinsam ums Überleben kämpfen ohne Angst haben zu müssen, dass plötzlich Fremde dazustoßen. Dieser Modus ist für 2 - 5 Spieler geeignet.'
            ),
            'type' => 'multi_custom',
            'slots' => array(2,5),
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),

            'jobs' => array(10010,10020,10030,10040,1080),
            'unstartable_jobs' => array(),

            'setup' => array(
                'inherit' => array(10000),
                'config' => array(),
                'spawn' => array(),
            ),
        ),
        10200 => array(
            'meta' => array(
                'name' => 'Survival Privat (groß)',
                'caption' => 'Der Modus für Spieler mit vielen Freunden.',
                'headline' => 'Haben Sie eine Reservierung?',
                'body' => 'In diesem Modus kannst du mit deinen Freunden gemeinsam ums Überleben kämpfen ohne Angst haben zu müssen, dass plötzlich Fremde dazustoßen. Dieser Modus ist für 6 - 10 Spieler geeignet.'
            ),
            'type' => 'multi_custom',
            'slots' => array(6,10),
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),

            'jobs' => array(10010,10020,10030,10040,1080),
            'unstartable_jobs' => array(),

            'setup' => array(
                'inherit' => array(10000),
                'config' => array(),
                'spawn' => array(),
            ),
        ),
        11000 => array(
            'meta' => array(
                'name' => 'Roadtrip',
                'caption' => 'Immer auf der Flucht vor Zombies',
                'headline' => 'Roadtrip',
                'body' => 'In diesem Spielmodus reist du mit einem klapprigen Wohnmobil durch die Welt. Versuche, so weit zu kommen wie möglich. Dieser Modus ist für 2 - 5 Spieler geeignet.'
            ),
            'type' => 'multi_custom',
            'slots' => array(2,5),
            'requirements' => array('mode' => array(),'job' => array(10010 => 150),'ext' => array(),'ext_note' => array()),

            'jobs' => array(10020,10030,10040,1080),
            'unstartable_jobs' => array(1080),

            'setup' => array(
                'inherit' => array(10000),
                'config' => array(
                    'game.config.map' => 'roadtrip_init',
                    'game.config.itemset' => 'roadtrip',

                    'ranking.points.zombie_kills.factor'=> 0,
                    'ranking.points.zombie_kills.offset'=> 0,
                    'ranking.points.survival.factor'    => 0,
                    'ranking.points.survival.offset'    => 0,
                    'ranking.points.home.factor'        => 1,
                    'ranking.points.home.offset'        => 0,
                    'ranking.points.home.stretch'       => 200000,
                    'ranking.points.home.threshold'     => -15,
                ),
                'spawn' => array(),
            ),
        ),
    ),
    'jobs' => array(
        0 => array(
            'meta' => array('name' => 'Alter Bürger','caption' => ''),
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),
            'levels' => array(),
            'setup' => array(
                'inherit' => array(),
                'f' => function($mode, $level) {
                        /** @global Model_Player $player */
                        global $player;

                        //Inventory
                        $player->inventory()->limit(100);

                        $player->get_status()->set(
                            Model_Status::MS_STAT_DRUNK,	0,
                            Model_Status::MS_STAT_ENERGY,	100,
                            Model_Status::MS_STAT_HEALTH,	100,
                            Model_Status::MS_STAT_HUNGER,	50,
                            Model_Status::MS_STAT_SLEEPY,	100,
                            Model_Status::MS_STAT_THIRST,	50
                        );

                        //Buffs
                        new Model_Buffs_Metabolism();
                        new Model_Buffs_Alcohol();
                        new Model_Buffs_Zombify();
                        new Model_Buffs_Nuclear();
                        new Model_Buffs_Heartbeat(null, ($mode == 2000) ? 2016 : -1);
                        new Model_Buffs_Backpack();
                        new Model_Buffs_Transport();
                        new Model_Buffs_Flashlight();
                        new Model_Buffs_Daytime();
                        new Model_Buffs_Freeze();

                        //Clothes
                        $clothes = new Model_Items_Clothes();
                        $player->inventory()->add($clothes);
                        $clothes->equip($player);

                        //Hero items
                        $player->inventory()->add(new Model_Items_Virtual_Hero_Common());
                    }
            )
        ),
        100 => array(
            'meta' => array('name' => 'Alter Soldat','caption' => ''),
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),
            'levels' => array(),
            'setup' => array('inherit' => array(0)),
        ),
        200 => array(
            'meta' => array('name' => 'Alter Pfadfinder','caption' => ''),
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),
            'levels' => array(),
            'setup' => array('inherit' => array(0)),
        ),
        300 => array(
            'meta' => array('name' => 'Alter Missionar','caption' => ''),
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),
            'levels' => array(),
            'setup' => array('inherit' => array(0)),
        ),
        400 => array(
            'meta' => array('name' => 'Alter Schnösel','caption' => ''),
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),
            'levels' => array(),
            'setup' => array('inherit' => array(0)),
        ),
        1010 => array(
            'meta' => array('name' => 'Bürger','caption' => ''),
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),
            'levels' => array(),
            'setup' => array(
                'inherit' => array(0),
                'f' => function($mode, $level) {
                        /** @global Model_Player $player */
                        global $player;

                        $player->inventory()->add(new Model_Items_Bottle);
                        $player->inventory()->add(new Model_Items_Cyanide);
                    }
            )
        ),
        1011 => array(
            'meta' => array(
                'name' => 'Einfacher Bürger',
                'caption' => 'Ein ganz normaler, sympathischer Kerl.',
            ),

            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(),
            'setup' => array('inherit' => array(1010), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Miniknife());
                }),
        ),
        1012 => array(
            'meta' => array(
                'name' => 'Einfache Bürgerin',
                'caption' => 'Eine ganz normale, sympathische Frau.',
            ),

            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(),
            'setup' => array('inherit' => array(1010), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->limit(110);
                    $player->inventory()->add(new Model_Items_Briefcase);
                }),
        ),
        1020 => array(
            'meta' => array(
                'name' => 'Ehemaliger Soldat',
                'caption' => 'Leidet an ein paar kleinen, unbedeutenden Kriegstraumata, ist dafür aber Meister im Einsatz von Waffen und Finden von Munition. In höheren Leveln hat er eventuel sogar ein kleines Kriegssouvenir dabei ...',
            ),

            'requirements' => array(
                'mode' => array('1000,1100' => 100),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(100, 250, 500, 1000, 2000),
            'setup' => array('inherit' => array(1010), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    new Model_Buffs_Job_Soldier(null, $level);
                    $player->inventory()->add(new Model_Items_Virtual_Hero_Soldier($level));
                    switch ($level)
                    {
                        case 3: case 4:	$player->inventory()->add(new Model_Items_Handgun); break;
                        case 5:			$player->inventory()->add(new Model_Items_Rifle); break;
                    }
                }),
        ),
        1030 => array(
            'meta' => array(
                'name' => 'Pfadfinder',
                'caption' => 'Führte eine besonders innige Beziehung mit seinem Gruppenleiter. Er findet jede Abkürzung, egal wie gut sie versteckt ist. In höheren Leveln kann er sogar versuchen, sich an Zombies vorbeizuschleichen.',
            ),

            'requirements' => array(
                'mode' => array('1000,1100' => 100),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(100, 250,  500, 1000, 2000),
            'setup' => array('inherit' => array(1010), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Virtual_Hero_Pathfinder($level));
                    new Model_Buffs_Job_Pathfinder(null, $level);
                }),
        ),
        1040 => array(
            'meta' => array(
                'name' => 'Missionar',
                'caption' => 'Hat die Bibel (und zur Sicherheit Koran und Tanach) auswendig gelernt und kann aus seinem Glauben Kraft schöpfen. Außerdem verfügt er über eine beeindruckende Weinsammlung und kann auf höheren Leveln im Kampf auf göttliche Unterstützung hoffen.',
            ),

            'requirements' => array(
                'mode' => array('1000,1100' => 100),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(100, 250,  500, 1000, 2000),
            'setup' => array('inherit' => array(1010), 'f' => function($mode, $level) {
                    /**
                     * @global Model_Player $player
                     * @global Model_Game $game
                     */
                    global $player, $game;
                    $player->inventory()->add(new Model_Items_Holybook);
                    $player->inventory()->add(new Model_Items_Virtual_Hero_Missionary($level));
                    if ($level >= 2)
                        for ($i = 0; $i < 6; $i++) $player->location()->inventory()->add(new Model_Items_Wine);
                    if ($level >= 3)
                        $game->map()->add_location('Model_Places_Cathedral');
                }),
        ),
        1050 => array(
            'meta' => array(
                'name' => 'Reicher Schnösel',
                'caption' => 'Musste nach der Apokalypse feststellen, dass er trotz Adelstitel immer noch rot blutet. Ist selbst in der Postapokalypse noch reicher als andere und startet mit zusätzlichen Rohstoffen. In höheren Leveln besitzt er einige zusätzliche Ausbauten in seinem Versteck.',
            ),

            'requirements' => array(
                'mode' => array('1000,1100' => 100),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(100, 250,  500, 1000, 2000),
            'setup' => array('inherit' => array(1010), 'f' => function($mode, $level) {
                    /**
                     * @global Model_Player $player
                     * @global Model_Game $game
                     */
                    global $player, $game;
                    $player->inventory()->add(new Model_Items_Virtual_Hero_Snot($level));
                    $player->get_status()->set(	Model_Status::MS_STAT_HUNGER,100,Model_Status::MS_STAT_THIRST,100);

                    if ($level >= 2)
                    {
                        $player->location()->inventory()->add(new Model_Items_Generic_Gwood);
                        $player->location()->inventory()->add(new Model_Items_Generic_Gmetal);
                    }
                    if ($level >= 3)
                        $game->map()->add_location('Model_Places_Villa');

                    if ($level >= 4)
                        Model_Blueprints::fast_apply(Tool_Scripts::home($game), 'upgrades', ['bedr1','manu1']);
                }),
        ),
        1060 => array(
            'meta' => array(
                'name' => 'Survivalist',
                'caption' => 'Hat vom Weltuntergang eigentlich nicht viel mitbekommen, da er sowieso die meiste Zeit in der Wildnis rumhockt. Ein Survivalist findet überall nützliches Zeug - und wenn es nicht nützlich ist, baut er was nützliches daraus!',
            ),

            'requirements' => array(
                'mode' => array('1000,1100' => 100),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(100, 250,  500, 1000, 2000),
            'setup' => array('inherit' => array(1010), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Virtual_Hero_Survivalist($level));
                }),
        ),
        1070 => array(
            'meta' => array(
                'name' => 'Muskelprotz',
                'caption' => 'Wurde mehrmals zum Mr. Universe gewählt, ist früher im Zirkus aufgetreten und saß dann 2 Jahre im Gefängnis, weil er Chuck Norris zusammengeschlagen hat. Jetzt setzt er seine unglaubliche Muskelkraft ein, um so ziemlich alles durch die Gegend zu schleppen.',
            ),

            'requirements' => array(
                'mode' => array('1000,1100' => 100),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(100, 250,  500, 1000, 2000),
            'setup' => array('inherit' => array(1010), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    switch ($level) {
                        case 1: case 2: $player->inventory()->limit(115); break;
                        case 3:			$player->inventory()->limit(125); break;
                        case 4:			$player->inventory()->limit(135); break;
                        case 5:			$player->inventory()->limit(150); break;
                        case 6:			$player->inventory()->limit(166); break;
                    }
                    $player->inventory()->add(new Model_Items_Virtual_Hero_Muscle($level));
                    if ($level > 1 && $level < 5)
                        $player->inventory()->add(new Model_Items_Bmt);
                    elseif ($level >= 5)
                        $player->inventory()->add(new Model_Items_Bmt2);
                }),
        ),
        1080 => array(
            'meta' => array(
                'name' => 'Kind',
                'caption' => 'Konnte den Zombies entkommen, indem es die Beschützerinstinkte seiner Eltern eiskalt ausnutzte - musste dann aber leider feststellen, das Erwachsene in manchen Situationen doch ganz hilfreich sind.',
            ),

            'requirements' => array(
                'mode' => array('10000,10100,10200' => 1000),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(),
            'setup' => array('inherit' => array(10000), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    new Model_Buffs_Metabolism2();
                    $player->inventory()->add(new Model_Items_Virtual_Hero_Child($level));
                    $player->inventory()->limit(50);
                    $player->inventory()->add(new Model_Items_Generic_Teddy());

                    $player->get_status()->scaling_add(Model_Status::MS_STAT_DRUNK, Model_Status::MS_EFFECT_GLOBAL, 'child_booze', 2.5);
                }),
        ),
        2000 => array(
            'setup' => array('inherit' => array(0), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->get_status()->set(	Model_Status::MS_STAT_HUNGER,	100,
                        Model_Status::MS_STAT_THIRST,	100);

                    $bottle = new Model_Items_Bottle;
                    $bottle->add_water(4, 0);
                    $player->inventory()->add($bottle);

                    $player->inventory()->add(new Model_Items_Paracetoid);
                    $player->inventory()->add(new Model_Items_Paracetin);
                    $player->inventory()->add(new Model_Items_Ammobelt);
                    $player->inventory()->add(new Model_Items_Bottle);
                    $player->inventory()->add(new Model_Items_Bottle);
                    $player->inventory()->add(new Model_Items_Cyanide);
                }),
        ),
        2010 => array(
            'meta' => array(
                'name' => 'Berserker',
                'caption' => 'Mit dieser Einstellung startest du dein Spiel mit einer einzigartigen, besonders starken Machete.',
            ),

            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(),
            'setup' => array('inherit' => array(2000), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Machete3);
                    $player->inventory()->add(new Model_Items_Batgun);
                }),
        ),
        2020 => array(
            'meta' => array(
                'name' => 'Eiskalter Killer',
                'caption' => 'Mit dieser Einstellung startest du dein Spiel mit einem einzigartigen, besonders starken Batteriewerfer.',
            ),

            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(),
            'setup' => array('inherit' => array(2000), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Machete);
                    $player->inventory()->add(new Model_Items_Batgun4);
                }),
        ),
        2030 => array(
            'meta' => array(
                'name' => 'Wutbürger',
                'caption' => 'Mit dieser Einstellung beginnst du das Spiel mit den maximal verbesserten Versionen deiner Standardwaffen.',
            ),

            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(),
            'setup' => array('inherit' => array(2000), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Machete2);
                    $player->inventory()->add(new Model_Items_Batgun3);
                }),
        ),
        3000 => array(
            'setup' => array('inherit' => array(0), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->get_status()->set(	Model_Status::MS_STAT_HUNGER,	60,
                        Model_Status::MS_STAT_THIRST,	75);

                    $bottle = new Model_Items_Bottle;
                    $bottle->add_water(4, 0);
                    $player->inventory()->add($bottle);

                    $player->inventory()->add(new Model_Items_Paracetoid);
                    $player->inventory()->add(new Model_Items_Paracetin);
                    $player->inventory()->add(new Model_Items_Ammobelt);
                    $player->inventory()->add(new Model_Items_Maptool);
                    $player->inventory()->add(new Model_Items_Lunchbox);
                    $player->inventory()->add(new Model_Items_Machete);
                    $player->inventory()->add(new Model_Items_Batgun);
                    $player->inventory()->add(new Model_Items_Cyanide);
                }),
        ),
        3010 => array(
            'meta' => array(
                'name' => 'Laie',
                'caption' => 'Tja, du hast beim Losen wohl die Arschkarte gezogen. Nicht nur, dass du aus deiner gemütlichen Stadt hierher geschickt wurdest, du hast noch nicht mal wirklich Ahnung von dem, was du hier tun sollst. Ob das gut gehen kann...?',
            ),

            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(),
            'setup' => array('inherit' => array(3000)),
        ),
        3020 => array(
            'meta' => array(
                'name' => 'Zeichner',
                'caption' => 'Man nennt dich den Picasso der Karten - hauptsächlich, weil du der einzige bist der sie lesen kann. Nichtsdestotrotz führst du den schnellsten Bleistift westlich von Glottisburg, ein paar Ruinen zu zeichnen sollte also ein Klacks für dich sein.',
            ),

            'requirements' => array(
                'mode' => array(3000 => 100),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(500, 1500),
            'setup' => array('inherit' => array(3000)),
        ),
        3030 => array(
            'meta' => array(
                'name' => 'Techniker',
                'caption' => 'Mit einem Zeichenbrett kannst du nicht wirklich was anfangen... dafür bist du ein Meister der Laservermessung. Wo ein einfacher Zeichner Stunden mit sinnlosem Gekritzel verbringt, erzeugst du in wenigen Sekunden eine präzise Messung. Vorrausgesetzt natürlich, du findest die entsprechenden Teile...',
            ),

            'requirements' => array(
                'mode' => array(3000 => 100),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(500, 1500),
            'setup' => array('inherit' => array(3000), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    if ($level >= 3) {
                        $player->inventory()->add(new Model_Items_Generic_Lasermapper);
                        $player->inventory()->add(new Model_Items_Generic_Lasermapper);
                    }
                }),
        ),
        4010 => array(
            'meta' => array(
                'name' => 'Gladiator',
                'caption' => '',
            ),

            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(),
            'setup' => array('inherit' => array(0), 'f' => function($mode, $level) {
                    /**
                     * @global Model_Player $player
                     * @global Model_Game $game
                     */
                    global $player, $game;
                    $bottle = new Model_Items_Bottle;
                    $bottle->add_water(2, 16);
                    $player->inventory()->add($bottle);

                    $player->inventory()->add(new Model_Items_Paracetoid);
                    $player->inventory()->add(new Model_Items_Paracetin);
                    $player->inventory()->add(new Model_Items_Miniknife);
                    $player->inventory()->add(new Model_Items_Cyanide);

                    $game->map()->add_location('Model_Places_Colosseum');
            }),
        ),
        10000 => array(
            'setup' => array('inherit' => array(0), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Bottle);
                    $player->inventory()->add(new Model_Items_Ammobelt());
                    $player->inventory()->add(new Model_Items_Batgun());
                }),
        ),
        10010 => array(
            'meta' => array(
                'name' => 'Politiker',
                'caption' => 'Nachdem die Welt den Bach heruntergegangen ist merkst du plötzlich, dass du eigentlich nichts kannst, nichts weißt und auch nicht sonderlich sympathisch bist. Als Politiker bist du im Wesentlichen ein Klotz am Bein der anderen Überlebenden... du kannst nur hoffen, dass sie sich nicht den Zombiehorden opfern.',
            ),

            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(),
            'setup' => array('inherit' => array(10000), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Machete());
                }),
        ),
        10020 => array(
            'meta' => array(
                'name' => 'Football-Coach',
                'caption' => 'Als Football-Coach bist du ein Meister subtiler Künste wie &quot;Frontal durch Zombiehorden brechen&quot;, außerdem kannst du im Kampf einiges einstecken, ohne dich zu verletzen. Mit ein bisschen Übung gelingt es dir eventuell sogar, eine Meute von belagernden Zombies so zu durchberechen, dass auch andere Spieler dir folgen können.',
            ),

            'requirements' => array(
                'mode' => array('1000,1100,3000,4000,10000,10100,10200,11000' => 50),
                'job' => array(10010 => 150),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(100, 200, 300, 500, 800, 1300, 2100, 3400, 5500),
            'setup' => array('inherit' => array(10000), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Virtual_Hero_Coach($level));
                    $player->inventory()->add(new Model_Items_Machete());
                    new Model_Buffs_Job_Coach(null, $level);
                }),
        ),
        10030 => array(
            'meta' => array(
                'name' => 'Medizinstudent',
                'caption' => 'Als Student hast du viele medizinische Studien durchgeführt, die meisten davon hatten mit den Auswirkungen von Alkohol und Drogen auf den eigenen Körper zu tun. Aus diesem Grund warst du leider etwas weniger Aufmerksam in den Vorlesungen. Aber zum Aufkleben von Pflastern reichen deine Kenntnisse gerade so noch aus.',
            ),

            'requirements' => array(
                'mode' => array('1000,1100,3000,4000,10000,10100,10200,11000' => 50),
                'job' => array(10010 => 150),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(100, 200, 300, 500, 800, 1300, 2100, 3400, 5500),
            'setup' => array('inherit' => array(10000), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Virtual_Hero_Student($level));
                    $player->inventory()->add(new Model_Items_Machete());
                    $player->inventory()->add(new Model_Items_Bandage());
                }),
        ),
        10040 => array(
            'meta' => array(
                'name' => 'Frauenrechtlerin',
                'caption' => 'Du bist eine Meisterin, lautstark gegen alles und jeden zu hetzen der dir nicht in den Kram passt - der einzige Grund, warum deine Mitüberlebenden dir noch keine Rohrzange über den Schädel gezogen haben, ist dass du dir in schöner Regelmäßigkeit Sprüche auf die Titten schreibst und dann stundenlang oben ohne gegen die Zombies protestierst. Dank deiner kratzfreudigen Fingernägel und dem Endlos-Vorrat Pfefferspray bist du aber auch im Kampf ganz brauchbar.',
            ),

            'requirements' => array(
                'mode' => array('1000,1100,3000,4000,10000,10100,10200,11000' => 50),
                'job' => array(10010 => 150),
                'ext' => array(),
                'ext_note' => array(),
            ),

            'levels' => array(100, 200, 300, 500, 800, 1300, 2100, 3400, 5500),
            'setup' => array('inherit' => array(10000), 'f' => function($mode, $level) {
                    /** @global Model_Player $player */
                    global $player;
                    $player->inventory()->add(new Model_Items_Virtual_Hero_Woman($level));
                    $player->inventory()->add(new Model_Items_Pepperspray());
                    new Model_Buffs_Job_Woman(null, $level);
                }),
        ),
    )
);