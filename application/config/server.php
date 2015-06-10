<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'season' => 7,

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