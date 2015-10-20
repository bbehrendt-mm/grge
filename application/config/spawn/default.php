<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
    'Model_Places_Bar'		            => Array('chance' =>  10, 'accum' =>  5, 'range' =>  4, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   1,  3)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  2,  5)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  2,  5)),
            Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   1,   1), 'distance' => Array(  2,  3)),
        ),
    )),
    'Model_Places_Burgerjoint'		    => Array('chance' =>  10, 'accum' =>  10, 'range' =>  10, 'groups' => Array(
        Array(
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   2), 'distance' => Array(   5,  10)),
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   2), 'distance' => Array(   5,  10)),
                Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  10,  15)),
        ),
        Array(
                Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  10,  15)),
                Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   1,   1), 'distance' => Array(  10,  15)),
        ),
        Array(
                Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   2,   3), 'distance' => Array(  30,  30)),
        ),
    )),
    'Model_Places_Toilet'		        => Array('chance' =>  15, 'accum' =>  30, 'range' =>  1, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   1,  1)),
        ),
    )),
    'Model_Places_Radio'				=> Array('chance' =>  15, 'accum' =>  40, 'range' =>  25, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   1,   1), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   0,   1), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   0,   1), 'distance' => Array(  50, 60)),
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
    )),
    'Model_Places_Home'					=> Array('chance' =>   10, 'accum' =>  7, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   1,  3)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   5), 'distance' => Array(   3,  5)),
        ),
    )),
    'Model_Places_Tentkit'					=> Array('chance' =>   20, 'accum' =>  5, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   6), 'distance' => Array(   1,  1)),
        ),
    )),
    'Model_Places_Tentkit2'					=> Array('chance' =>   20, 'accum' =>  5, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   6), 'distance' => Array(   1,  1)),
        ),
    )),
    'Model_Places_Hotel'				=> Array('chance' =>   15, 'accum' =>  15, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   1,  3)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   5), 'distance' => Array(   3,  5)),
        ),
    )),
    'Model_Places_House'				=> Array('chance' =>   45, 'accum' =>  95, 'range' =>   5, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   1,  3)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   5), 'distance' => Array(   3,  5)),
        ),
    )),
    'Model_Places_Warehouse'			=> Array('chance' =>   10, 'accum' =>  15, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   8, 10)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   8, 10)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Fatass',	    'num' => Array(   1,   1), 'distance' => Array(   8, 10)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2, 10)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Runner',	    'num' => Array(   1,   1), 'distance' => Array(  10, 15)),
        ),
    )),
    'Model_Places_Vault'		    	=> Array('chance' =>   30, 'accum' =>  30, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   5), 'distance' => Array(   8, 10)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   2,   6), 'distance' => Array(   8, 10)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Fatass',	    'num' => Array(   1,   1), 'distance' => Array(   8, 10)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2, 10)),
            Array('type' => 'Model_Combat_Zombies_Runner',  	'num' => Array(   1,   1), 'distance' => Array(   8, 10)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Runner',	    'num' => Array(   1,   3), 'distance' => Array(  10, 15)),
        ),
    )),
    'Model_Places_Hospital_Private'	    => Array('chance' =>  15, 'accum' => 90, 'range' =>  3, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(  3,   20), 'distance' => Array(  50, 90)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,  10), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   1,   2), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   0,   3), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   0,   1), 'distance' => Array(  50, 60)),
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   1,    5), 'distance' => Array(  20, 60)),
            Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   0,    1), 'distance' => Array(  20, 30)),
        ),
    )),
    'Model_Places_Weaponshop'			=> Array('chance' =>  20, 'accum' =>  50, 'range' =>  15, 'groups' => Array(
        Array(
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   5,   8)),
                Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   0,   1), 'distance' => Array(  10,  15)),
        ),
    )),
    'Model_Places_Store'				=> Array('chance' =>  10, 'accum' =>  70, 'range' =>   0, 'groups' => Array(
        Array(
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   5,   8)),
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   2,   3)),
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   1,   1)),
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   1,   1)),
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   1,   1)),
        ),
    )),
    'Model_Places_Villa'				=> Array('chance' =>  10, 'accum' =>  30, 'range' =>  15, 'groups' => Array(
        Array(
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   0,   1)),
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   0,   1)),
                Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   0,   1)),
                Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   0,   1), 'distance' => Array(  10,  50)),
        ),
    )),
    'Model_Places_Roadtrip_Garage'		=> Array('chance' =>  20, 'accum' =>  10, 'range' =>  30,	'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Mutant',		'num' => Array(   1,   4), 'distance' => Array( 10, 50)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   2), 'distance' => Array( 5,  50)),
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   1,   1), 'distance' => Array( 10, 50)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Mutant',		'num' => Array(   0,   6), 'distance' => Array( 10,  50)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Mutant',		'num' => Array(   0,   1), 'distance' => Array( 10, 50)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   0,   1), 'distance' => Array( 5,  50)),
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   0,   1), 'distance' => Array( 10, 50)),
        ),
    )),
    'Model_Places_Roadtrip_Myhouse'					=> Array('chance' =>   15, 'accum' =>  12, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   1,  3)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   1,   5), 'distance' => Array(   3,  5)),
        ),
    )),
    'Model_Places_Roadtrip_Roadblock'	=> Array('chance' =>   5, 'accum' =>  30, 'range' =>  50, 'groups' => Array(
        Array(
            Array('type' => 'Model_Combat_Zombies_Shambler',	'num' => Array(   5,   8), 'distance' => Array(  10,   20)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Runner',		'num' => Array(   2,   4), 'distance' => Array(  50,  80)),
        ),
        Array(
            Array('type' => 'Model_Combat_Zombies_Fatass',		'num' => Array(   3,   5), 'distance' => Array(  5,   10)),
        ),
    )),
    'Model_Places_Asylumhideout'	=> Array('chance' =>   0, 'accum' =>  0, 'range' =>   0, 'groups' => Array()),
);