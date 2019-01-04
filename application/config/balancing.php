<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'pause' => Array(
		'min_duration' => 1800,         // 30 Minutes
		'min_interval' => 1800,        // 1.5 Hours
	),
    'shop' => array(
        'enabled' => true,
        'free_coins' => 250
    ),
    'mentor' => array(
        'sp_threshold' => 100,
        'sp_bc_factor' => 0.05,
    )
);