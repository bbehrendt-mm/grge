<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'season' => 9,

    'titles' => [
        0 => 'Fröhliche Betazeit',
        1 => 'Der Anfang',
        2 => 'Gender-Mainstreaming',
        3 => 'Kriegszustand',
        4 => 'Tod unter Freunden',
        5 => 'Unter freiem Himmel',
        6 => 'Heldenhafte Kinder',
        7 => 'Evolution',
        8 => 'Freunde und Feinde',
        9 => 'Schöner sterben'
    ],

    'downtime' => [[10,15]],        // ONE downtime, starting 10 minutes after midnight, lasting until 15 minutes after midnight

    'externals' => array(
        'cronjob' => array(
            'token' => 'c1bdd255c06ba548e79e69e639064c474e53537c90895817680fa04382a1863a',
            'from' => 'watchdog',
            'to' => array('kontakt@ruine.dvspot.de'),
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