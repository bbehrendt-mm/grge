<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'multiplayer' => array(
		'capacity' => 5,
		'parallel_games' => array(
            'de' => 2,
            'en' => 3
        ),
        'mp_lockouts' => array(
            'time' => '1 week',
            'max_count' => 3,
        )
	),
    'access' => array(
        'whitelisting' => true,
    ),
    'event' => null
);