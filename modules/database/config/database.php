<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'default' => array
	(
		'type'       => 'MySQL',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname     server hostname, or socket
			 * string   database     database name
			 * string   username     database username
			 * string   password     database password
			 * boolean  persistent   use persistent connections?
			 * array    variables    system variables as "key => value" pairs
			 *
			 * Ports and sockets may be appended to the hostname.
			 */
			'hostname'   => 'localhost',
			'database'   => 'game',
			'username'   => 'grgedev',
			'password'   => '6U6yrYRqzdYQa7UE',
			'persistent' => FALSE,
		),
		'table_prefix' => 'grg_',
		'charset'      => 'utf8',
		'caching'      => FALSE,
	),
);
