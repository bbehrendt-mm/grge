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
                    'places.outworld.spawn_dogmeat'         => false,
                    'places.outworld.spawn_stranger'        => false,
                    'places.outworld.alt_spawn_stranger'    => false,
                    'places.outworld.location_density'      => 1,
                    'places.bar.spawn_winchester'           => false,
                    'places.toilet.spawn_sherri'            => false,
                    'places.general.spawn_random_animals'   => true,
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
                    'game.lobby.persistent'                 => false,
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
                    'places.outworld.spawn_dogmeat'         => true,
                    'places.bar.spawn_winchester'           => true,
                    'places.toilet.spawn_sherri'            => true,

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
                    'places.dryout_factor'                  => 1.5,
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
                    'places.outworld.spawn_dogmeat'     => true,
                    'places.bar.spawn_winchester'       => true,
                    'places.toilet.spawn_sherri'        => true,

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
                    'places.outworld.spawn_dogmeat'     => true,
                    'places.bar.spawn_winchester'       => true,
                    'places.toilet.spawn_sherri'        => true,
                    'places.general.spawn_random_animals' => true,

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
        12000 => array(
            'meta' => array(
                'name' => 'Townships!',
                'caption' => 'Spiel und Spaß für die ganze Stadt!',
                'headline' => '',
                'body' => ''
            ),
            'type' => 'special_multi_auto',
            'requirements' => array('mode' => array(),'job' => array(),'ext' => array(),'ext_note' => array()),

            'jobs' => array(12010,12020,12030,12040,12050,12060,12070),
            'unstartable_jobs' => array(),

            'setup' => array(
                'inherit' => array(0),
                'config' => array(
                    'places.outworld.spawn_stranger'    =>  false,
                    'places.outworld.alt_spawn_stranger'=>  true,
                    'places.outworld.spawn_dogmeat'     => true,
                    'places.bar.spawn_winchester'       => true,
                    'places.toilet.spawn_sherri'        => true,

                    'ranking.points.zombie_kills.factor'=>  0,
                    'ranking.points.zombie_kills.offset'=>  0,
                    'ranking.points.survival.factor'    =>  1,
                    'ranking.points.survival.offset'    =>  0,
                    'ranking.points.home.factor'        =>  0,
                    'ranking.points.home.offset'        =>  0,
                    'ranking.points.home.stretch'       =>  1,
                    'ranking.points.home.threshold'     =>  0,

                    'game.lobby.persistent'             =>  true,
                    'modules.multiplayer'               =>  true,
                    'game.bhav.infections'              =>  true,

                    'game.config.map'                   => 'township',
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
                        //Inventory
                        Globals::CurrentPlayer()->inventory()->limit(100);

                        Globals::CurrentPlayer()->get_status()->set(
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
                        Globals::CurrentPlayer()->inventory()->add($clothes);
                        $clothes->equip(Globals::CurrentPlayer());

                        //Hero items
                        Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Common());
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
                        Globals::CurrentPlayer()->inventory()->add(new Model_Items_Bottle);
                        Globals::CurrentPlayer()->inventory()->add(new Model_Items_Cyanide);
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
                    $item = new Model_Items_Miniknife();
                    Globals::CurrentPlayer()->inventory()->add($item);
                    $item->equip(Globals::CurrentPlayer());
                    Globals::CurrentPlayerActual()->battle_stats([4,6,null,null]); // INI ATK DEF ACC
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
                    Globals::CurrentPlayer()->inventory()->limit(110);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Briefcase);
                    Globals::CurrentPlayerActual()->battle_stats([6,4,null,null]); // INI ATK DEF ACC
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
                    new Model_Buffs_Job_Soldier(null, $level);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Soldier($level));
                    $item = null;
                    Globals::CurrentPlayer()->battle_stats([null,6,null,null]); // INI ATK DEF ACC
                    switch ($level) {
                        case 3: case 4:	$item = new Model_Items_Handgun(); Globals::CurrentPlayerActual()->battle_stats([null,null,null,6]); break;
                        case 5:			$item = new Model_Items_Rifle(); Globals::CurrentPlayerActual()->battle_stats([null,null,null,8]); break;
                    }
                    if ($item) {
                        Globals::CurrentPlayer()->inventory()->add($item);
                        $item->equip(Globals::CurrentPlayer());
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
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Pathfinder($level));
                    new Model_Buffs_Job_Pathfinder(null, $level);
                    Globals::CurrentPlayerActual()->battle_stats([7,null,null,null]); // INI ATK DEF ACC
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
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Holybook);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Missionary($level));
                    Globals::CurrentPlayerActual()->battle_stats([6,4,4,4]); // INI ATK DEF ACC
                    if ($level >= 2)
                        for ($i = 0; $i < 6; $i++) Globals::CurrentPlayer()->location()->inventory()->add(new Model_Items_Wine);
                    if ($level >= 3)
                        Globals::CurrentGame()->map()->add_location('Model_Places_Cathedral');
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
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Snot($level));
                    Globals::CurrentPlayer()->get_status()->set(	Model_Status::MS_STAT_HUNGER,100,Model_Status::MS_STAT_THIRST,100);

                    if ($level >= 2)
                    {
                        Globals::CurrentPlayer()->location()->inventory()->add(new Model_Items_Generic_Gwood);
                        Globals::CurrentPlayer()->location()->inventory()->add(new Model_Items_Generic_Gmetal);
                    }
                    if ($level >= 3)
                        Globals::CurrentGame()->map()->add_location('Model_Places_Villa');

                    if ($level >= 4) {
                        Tool_Scripts::home(Globals::CurrentGame())->setup_new_room(Tool_Scripts::home(Globals::CurrentGame())->create_new_room(10, ['inside']), ['bedroom'], ['bedr1']);
                        Tool_Scripts::home(Globals::CurrentGame())->setup_new_room(Tool_Scripts::home(Globals::CurrentGame())->create_new_room(10, ['inside']), ['workshop'], []);
                    }
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
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Survivalist($level));
                    Globals::CurrentPlayerActual()->battle_stats([null,null,6,null]); // INI ATK DEF ACC
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
                    switch ($level) {
                        case 1: case 2: Globals::CurrentPlayer()->inventory()->limit(115); break;
                        case 3:			Globals::CurrentPlayer()->inventory()->limit(125); break;
                        case 4:			Globals::CurrentPlayer()->inventory()->limit(135); break;
                        case 5:			Globals::CurrentPlayer()->inventory()->limit(150); break;
                        case 6:			Globals::CurrentPlayer()->inventory()->limit(166); break;
                    }
                    Globals::CurrentPlayerActual()->battle_stats([null,8,3,4]); // INI ATK DEF ACC
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Muscle($level));
                    if ($level > 1 && $level < 5)
                        Globals::CurrentPlayer()->inventory()->add(new Model_Items_Bmt);
                    elseif ($level >= 5)
                        Globals::CurrentPlayer()->inventory()->add(new Model_Items_Bmt2);
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
                    new Model_Buffs_Metabolism2();
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Child($level));
                    Globals::CurrentPlayer()->inventory()->limit(50);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Generic_Teddy());

                    Globals::CurrentPlayer()->get_status()->scaling_add(Model_Status::MS_STAT_DRUNK, Model_Status::MS_EFFECT_GLOBAL, 'child_booze', 2.5);
                    Globals::CurrentPlayerActual()->battle_stats([12,2,2,null]); // INI ATK DEF ACC
                }),
        ),
        2000 => array(
            'setup' => array('inherit' => array(0), 'f' => function($mode, $level) {
                    Globals::CurrentPlayer()->get_status()->set(	Model_Status::MS_STAT_HUNGER,	100,
                        Model_Status::MS_STAT_THIRST,	100);

                    $bottle = new Model_Items_Bottle;
                    $bottle->add_water(4, 0);
                    Globals::CurrentPlayer()->inventory()->add($bottle);

                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Paracetoid);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Paracetin);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Ammobelt);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Bottle);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Bottle);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Cyanide);
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
                    $items = [new Model_Items_Machete3(), new Model_Items_Batgun()];
                    /** @var Model_Items_Abstract_Equipable $item */
                    foreach ($items as $item) {
                        Globals::CurrentPlayer()->inventory()->add($item);
                        $item->equip(Globals::CurrentPlayer());
                    }
                    Globals::CurrentPlayerActual()->battle_stats([null,6,6,4]); // INI ATK DEF ACC
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
                    $items = [new Model_Items_Machete(), new Model_Items_Batgun4()];
                    /** @var Model_Items_Abstract_Equipable $item */
                    Globals::CurrentPlayerActual()->battle_stats([6,4,null,6]); // INI ATK DEF ACC
                    foreach ($items as $item) {
                        Globals::CurrentPlayer()->inventory()->add($item);
                        $item->equip(Globals::CurrentPlayer());
                    }
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
                    $items = [new Model_Items_Machete2(), new Model_Items_Batgun3()];
                    /** @var Model_Items_Abstract_Equipable $item */
                    Globals::CurrentPlayerActual()->battle_stats([null,6,null,6]); // INI ATK DEF ACC
                    foreach ($items as $item) {
                        Globals::CurrentPlayer()->inventory()->add($item);
                        $item->equip(Globals::CurrentPlayer());
                    }
                }),
        ),
        3000 => array(
            'setup' => array('inherit' => array(0), 'f' => function($mode, $level) {
                    Globals::CurrentPlayer()->get_status()->set(	Model_Status::MS_STAT_HUNGER,	60,
                        Model_Status::MS_STAT_THIRST,	75);

                    $bottle = new Model_Items_Bottle;
                    $bottle->add_water(4, 0);
                    Globals::CurrentPlayer()->inventory()->add($bottle);

                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Paracetoid);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Paracetin);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Ammobelt);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Maptool);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Lunchbox);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Cyanide);

                    $items = [new Model_Items_Machete(), new Model_Items_Batgun()];
                    /** @var Model_Items_Abstract_Equipable $item */
                    foreach ($items as $item) {
                        Globals::CurrentPlayer()->inventory()->add($item);
                        $item->equip(Globals::CurrentPlayer());
                    }

                    Globals::CurrentPlayerActual()->battle_stats([10,4,4,7]); // INI ATK DEF ACC
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
                    if ($level >= 3) {
                        Globals::CurrentPlayer()->inventory()->add(new Model_Items_Generic_Lasermapper);
                        Globals::CurrentPlayer()->inventory()->add(new Model_Items_Generic_Lasermapper);
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
                    $bottle = new Model_Items_Bottle;
                    $bottle->add_water(2, 16);
                    Globals::CurrentPlayer()->inventory()->add($bottle);

                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Paracetoid);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Paracetin);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Cyanide);

                    $item = new Model_Items_Miniknife();
                    Globals::CurrentPlayer()->inventory()->add($item);
                    $item->equip(Globals::CurrentPlayer());

                    Globals::CurrentPlayerActual()->battle_stats([6,6,6,6]); // INI ATK DEF ACC

                    Globals::CurrentGame()->map()->add_location('Model_Places_Colosseum');
            }),
        ),
        10000 => array(
            'setup' => array('inherit' => array(0), 'f' => function($mode, $level) {
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Bottle);
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Ammobelt());

                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Cyanide());
                    $item = new Model_Items_Batgun();
                    Globals::CurrentPlayer()->inventory()->add($item);
                    $item->equip(Globals::CurrentPlayer());
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
                    $item = new Model_Items_Machete();
                    Globals::CurrentPlayer()->inventory()->add($item);
                    $item->equip(Globals::CurrentPlayer());
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
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Coach($level));
                    $item = new Model_Items_Machete();
                    Globals::CurrentPlayer()->inventory()->add($item);
                    $item->equip(Globals::CurrentPlayer());
                    new Model_Buffs_Job_Coach(null, $level);
                    Globals::CurrentPlayerActual()->battle_stats([null,null,5 + floor($level/2),null]); // INI ATK DEF ACC
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
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Student($level));
                    $item = new Model_Items_Machete();
                    Globals::CurrentPlayer()->inventory()->add($item);
                    $item->equip(Globals::CurrentPlayer());
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Bandage());
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
                    Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Woman($level));
                    $item = new Model_Items_Pepperspray();
                    Globals::CurrentPlayer()->inventory()->add($item);
                    $item->equip(Globals::CurrentPlayer());
                    Globals::CurrentPlayerActual()->battle_stats([null,5 + floor($level/2),null,null]); // INI ATK DEF ACC
                    new Model_Buffs_Job_Woman(null, $level);
                }),
        ),

        12000 => array(
            'setup' => array('inherit' => array(0), 'f' => function($mode, $level) {
                Globals::CurrentPlayer()->inventory()->add(new Model_Items_Bottle);
                Globals::CurrentPlayer()->inventory()->add(new Model_Items_Ammobelt());

                Globals::CurrentPlayer()->inventory()->add(new Model_Items_Cyanide());

                $item = new Model_Items_Batgun();
                Globals::CurrentPlayer()->inventory()->add($item);
                $item->equip(Globals::CurrentPlayer());

                $item2 = new Model_Items_Machete();
                Globals::CurrentPlayer()->inventory()->add($item2);
                $item2->equip(Globals::CurrentPlayer());
            }),
        ),
        12010 => array(
            'meta' => array(
                'name' => 'Bürger',
                'caption' => 'Du bist das Rückrad jeder Stadt. Du verfügst über keine besonderen Fähigkeiten, daher eignest du dich perfekt für Selbstmord-Aktionen und als Zombiefutter!',
                'sign' => 'basic',
            ),
            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),
            'levels' => array(10,100,500,1000,2000,5000),
            'setup' => array('inherit' => array(12000), 'f' => function($mode, $level) {
                /** @global Model_Player $player */
                Globals::CurrentPlayer()->inventory()->add(new Model_Items_Stash(min(6,$level*2)));
                if ($level >= 3) Globals::CurrentPlayer()->inventory()->add(new Model_Items_Stash2(min(6,($level-2)*2)));
                if ($level >= 6) Globals::CurrentPlayer()->inventory()->add(new Model_Items_Stash3(min(6,($level-5)*3)));
                
            }),
        ),
        12020 => array(
            'meta' => array(
                'name' => 'Buddler',
                'caption' => 'Deine Erfahrung im Finden von Gegenständen macht dich zu einem wertvollen Mitglied der Gesellschaft. Und wenns mal nichts mehr zu finden gibt, kannst du den Zombies mit deiner Schaufel immer noch irgendlich die Visage umfurchen.',
                'sign' => 'collec',
            ),
            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),
            'levels' => array(10,100,500,1000,2000,5000),
            'setup' => array('inherit' => array(12000), 'f' => function($mode, $level) {
                Globals::CurrentPlayer()->get_status()->set_fixed_threshold(Model_Status::MS_CHAR_ITEM_SPAWNRATE, 1 + 0.15 * $level);
            }),
        ),
        12030 => array(
            'meta' => array(
                'name' => 'Wächter',
                'caption' => 'Mit deinem mächtigen Schild verteidigst du deine Mitbürger tapfer vor heranrückenden Zombiemeuten. Außerdem ist das Teil ein großartiger Sonnenschutz!',
                'sign' => 'guardian',
            ),
            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),
            'levels' => array(10,100,500,1000,2000,5000),
            'setup' => array('inherit' => array(12000), 'f' => function($mode, $level) {
                $item = new Model_Items_Guardshield($level);
                Globals::CurrentPlayer()->inventory()->add($item);
                $item->equip(Globals::CurrentPlayer());
            }),
        ),
        12040 => array(
            'meta' => array(
                'name' => 'Einsiedler',
                'caption' => 'Du warst nie ein Freund größerer Menschenmengen und hast Siedlungen bisher immer gemieden. Daher hast du gelernt, dich von dem Tau von Blättern und den Insekten in deinem Bart zu ernähren.',
                'sign' => 'hunter',
            ),
            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),
            'levels' => array(10,100,500,1000,2000,5000),
            'setup' => array('inherit' => array(12000), 'f' => function($mode, $level) {
                Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Hunter($level));
            }),
        ),
        12050 => array(
            'meta' => array(
                'name' => 'Aufklärer',
                'caption' => 'Lautlos wie ein Schatten schleichst du an Zombiehorden vorbei. Leider machst du dich damit nur bedingt bei deinen Mitbürgern beliebt, die du umzingelt zurücklässt...',
                'sign' => 'eclair',
            ),
            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),
            'levels' => array(10,100,500,1000,2000,5000),
            'setup' => array('inherit' => array(12000), 'f' => function($mode, $level) {
                Globals::CurrentPlayer()->get_status()->set_fixed_threshold(Model_Status::MS_CHAR_LOCATION_SPAWNRATE, 1 + 0.15 * $level);
                Globals::CurrentPlayer()->inventory()->add(new Model_Items_Virtual_Hero_Eclair($level));
            }),
        ),

        12060 => array(
            'meta' => array(
                'name' => 'Dompteur',
                'caption' => 'Dein Malteser ist dein treuster Begleiter, der dir Gegenstände hinterherschleppt und dich vor Zombies verteidigt. Und wenn mal die Nahrung knapp wird, schmeckt er bestimmt auch ganz passabel...',
                'sign' => 'tamer',
            ),
            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),
            'levels' => array(10,100,500,1000,2000,5000),
            'setup' => array('inherit' => array(12000), 'f' => function($mode, $level) {
                $npc = new Model_NPC_Special_Doodle($level);
                $npc->location_class(Globals::CurrentPlayer()->location_class());
                Globals::CurrentGame()->add_npc($npc);
                Globals::CurrentPlayer()->location()->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $npc->id(), true));
            }),
        ),

        12070 => array(
            'meta' => array(
                'name' => 'Techniker',
                'caption' => 'Du kennst die Unterschiede zwischen Mutter und Schraube, Pluspol und Minuspol, Linux und Unix und weist, dass man niemals die Ströme kreuzen sollte. Für so jemanden sind ein paar Versteck-Upgrades doch eine Kleinigkeit!',
                'sign' => 'tech',
            ),
            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),
            'levels' => array(10,100,500,1000,2000,5000),
            'setup' => array('inherit' => array(12000), 'f' => function($mode, $level) {
                Globals::CurrentPlayer()->get_status()->scaling_add(Model_Status::MS_STAT_ENERGY, Model_Status::MS_EFFECT_REQUIREMENT, 'tech', 1 - 0.1 * $level);
            }),
        ),

        12080 => array(
            'meta' => array(
                'name' => 'Schamane',
                'caption' => 'Du hast vor ein paar Jahren im Urlaub in Brasilien mal einen 2tägigen Voodoo-Kurs besucht und dir danach im Kostümgeschäft eine billige Plastik-Schamanenmaske gekauft. Im Prinzip ist das doch alles, was man als Qualifikation benötigt, oder?',
                'sign' => 'shaman',
            ),
            'requirements' => array(
                'mode' => array(),
                'job' => array(),
                'ext' => array(),
                'ext_note' => array(),
            ),
            'levels' => array(10,100,500,1000,2000,5000),
            'setup' => array('inherit' => array(12000), 'f' => function($mode, $level) {
                Globals::CurrentPlayer()->inventory()->add(new Model_Items_Mask($level));
            }),
        ),
    )
);