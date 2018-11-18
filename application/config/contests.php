<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'1301_dota2' => array(
		'start' => 1358722800,
		'end' => 1361142000,
		'name' => 'DotA2 Survival',
		'sdesc' => 'Gewinne einen von 3 DotA2-Keys!',
		'idesc' => 'Du nimmst am DotA2 Survival Wettbewerb teil. <b>Viel Glück!</b>',
		'gameid' => 991301,
		'basemode' => 1000,
		'speedrange' => Array(3,4,5,6),
		'subrange' => Array(1,2),
		'result' => function($gameobject) {if (!Tool_System::instance_of($gameobject, 'Model_Game')) return 0; return $gameobject->duration();}
	)
);