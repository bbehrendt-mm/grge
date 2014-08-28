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
        'whitelist' => array(
            'cs' => true,
            'de' => Array(
                31883	=> true,	//Brainbox
                34344	=> true,	//Krummy
                111	    => true,	//NobbZ
                35039	=> true,	//MisterD
                41947	=> true,	//Valky
                24343	=> true,	//Morrighan
                5402	=> true,	//OnlyIBash
                45549	=> true,	//Tarcat
                49121	=> true,	//cethegus
		        34966	=> true,	//Kazumi
		        4214	=> true,	//Witch23
			61084	=> true,	//Yolan
			51791	=> true,	//chroniX
			54671	=> true,	//Granizo
            ),
            'en' => Array(
                242095	=> true,	//Ika_Musume
                220604	=> true,	//Lyracorn
                265829	=> true,	//Nickboom
                226131	=> true,	//UndeadBurgerKng
                327804	=> true,	//ViciousCrow
                303532  => true,    //xRocketLlama
                304602  => true,     //YtterbiJum
		75160	=> true,
		324978	=> true,	//Camarelle
		303602	=> true,	//ThinkableDread
            ),
        ),
    ),
);