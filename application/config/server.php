<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'season' => 7,
	//ToDo: Integrate new version system
    'version' => array(
		'revision' => 0,
		'type' 	   => 'n',
		'date'	   => '?',
	),

    'downtime' => [[10,15]],        // ONE downtime, starting 10 minutes after midnight, lasting until 15 minutes after midnight

    'externals' => array(
        'chat' => array(
            'url' => 'http://localhost:8081/subsidiary/chat/live.php',
            'gui' => 'http://localhost:8081/subsidiary/chat/index.php',
            'token' => '03618f247618a11c6ea3e7605ab953fe61994d4ec39dee73bd9a1d54746fd7d6',
        ),
        'cronjob' => array(
            'token' => 'c1bdd255c06ba548e79e69e639064c474e53537c90895817680fa04382a1863a',
            'from' => 'watchdog',
            'to' => array('kontakt@ruine.dvspot.de'),
        )
    ),
    'links' => array(
        'forum' => array(
            'de' => 'http://forum.zombvival.de',
            'en' => 'http://forum.zombvival.de',
        ),
        'wiki' => array(
            'de' => 'http://ger.zv-wiki.net/',
            'en' => 'http://eng.zv-wiki.net',
        ),
        'chat' => array(
            'de' => 'http://chat.mibbit.com/?server=irc.Mibbit.Net&channel=%23ZombVival',
            'en' => 'http://chat.mibbit.com/?server=irc.Mibbit.Net&channel=%23ZombVival_EN',
        ),
        'help' => array(
            'de' => '/subsidiary/help/Startseite',
            'en' => '/subsidiary/help/Main_Page',
        )
    ),
	'io' => array(
		'security' => array(
			'unisessions' => true,
		),
		'performance' => array(
			'use_cloud' => true,
			'extended_cloud_cache' => false,
			'force_main_rewrite' => true,
			'force_revalidate' => true,
			'compression_level' => 9,
            'output_compression' => true,
		),
	),
	'debug' => array(
		'deploy_magic_box' => true,
	),
);

/**
 * Appendix:
 * d	In development
 * n	Nightly
 * a	Alpha
 * b	Beta
 * rc	Release Candidate
 * s	Stable
 */