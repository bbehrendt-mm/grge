<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
    'Model_Places_Bar'		            => Array('chance' =>  10, 'accum' =>  5, 'range' =>  4, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   1,  3)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  2,  5)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  2,  5)),
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(  2,  3)),
        ),
    )),
    'Model_Places_Camping_Grill'		=> Array('chance' =>  10, 'accum' =>  5, 'range' =>  50, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   1,  3)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  2,  5)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  2,  5)),
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(  2,  3)),
        ),
    )),
    'Model_Places_Camping_Tent'		    => Array('chance' =>  15, 'accum' =>  10, 'range' =>  2, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Runner',	'num' => Array(   1,   1), 'distance' => Array(   1,  1)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',	'num' => Array(   1,   1), 'distance' => Array(  1,  1)),
        ),
        Array(
            Array('type' => 'Model_Battle_Shambler',    'num' => Array(   1,   1), 'distance' => Array(  1,  1)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(  1,  1)),
        ),
    )),
    'Model_Places_Camping_Caravan'	    => Array('chance' =>  25, 'accum' =>  30, 'range' =>  3, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Runner',	'num' => Array(   1,   1), 'distance' => Array(   1,  5)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',	'num' => Array(   1,   1), 'distance' => Array(  1,  5)),
        ),
        Array(
            Array('type' => 'Model_Battle_Shambler',    'num' => Array(   1,   1), 'distance' => Array(  1,  5)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(  1,  5)),
        ),
    )),
    'Model_Places_Burgerjoint'		    => Array('chance' =>  10, 'accum' =>  10, 'range' =>  10, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   2), 'distance' => Array(   5,  10)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   2), 'distance' => Array(   5,  10)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  10,  15)),
        ),
        Array(
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  10,  15)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(  10,  15)),
        ),
        Array(
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   2,   3), 'distance' => Array(  30,  30)),
        ),
    )),
    'Model_Places_Burnedhouse'		    => Array('chance' =>  15, 'accum' =>  30, 'range' =>  10, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(   10,  20)),
        ),
    )),
    'Model_Places_Treehouse'		    => Array('chance' =>  0, 'accum' =>  40, 'range' =>  20, 'groups' => Array(
    )),
    'Model_Places_House_Cellar'		    => Array('chance' =>  20, 'accum' =>   5, 'range' =>   0, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   2), 'distance' => Array(  20,  22)),
        ),
        Array(
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  11,  13)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(   1,   3)),
        ),
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   3,  10)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   3,  10)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   5,  15)),
        ),
    )),
    'Model_Places_Camping_Office'		=> Array('chance' =>  15, 'accum' =>   10, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   2), 'distance' => Array(  20,  22)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  11,  13)),
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(   1,   3)),
        ),
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   3,  10)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   3,  10)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   5,  15)),
        ),
    )),
    'Model_Places_House_Firstfloor'		=> Array('chance' =>  10, 'accum' =>  50, 'range' =>  10, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   10), 'distance' => Array(   3,  30)),
        ),
    )),
    'Model_Places_Toilet'		        => Array('chance' =>  15, 'accum' =>  30, 'range' =>  1, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   1,  1)),
        ),
    )),
    'Model_Places_House_Secondfloor'	=> Array('chance' =>   5, 'accum' =>   5, 'range' =>  15, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(  15,  15)),
        ),
    )),
    'Model_Places_House_Hobby'			=> Array('chance' =>   0, 'accum' =>   0, 'range' =>  0,	'groups' => Array(
    )),
    'Model_Places_Constructionsite'		=> Array('chance' =>  05, 'accum' =>  90, 'range' =>  50, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
        Array(
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
    )),
    'Model_Places_Diy'					=> Array('chance' =>  15, 'accum' =>  90, 'range' =>  20, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
        Array(
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,   1), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   0,   1), 'distance' => Array(  50, 60)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
    )),
    'Model_Places_Camping'				=> Array('chance' =>  10, 'accum' =>  20, 'range' =>  30, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
        ),
        Array(
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,   1), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
    )),
    'Model_Places_Radio'				=> Array('chance' =>  15, 'accum' =>  40, 'range' =>  25, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
        Array(
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,   1), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   0,   1), 'distance' => Array(  50, 60)),
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
    )),
    'Model_Places_Home'					=> Array('chance' =>   10, 'accum' =>  7, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   1,  3)),
        ),
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
        ),
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(   3,  5)),
        ),
    )),
    'Model_Places_Hotel'				=> Array('chance' =>   15, 'accum' =>  15, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   1,  3)),
        ),
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
        ),
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(   3,  5)),
        ),
    )),
    'Model_Places_House'				=> Array('chance' =>   45, 'accum' =>  95, 'range' =>   5, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   1,  3)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   1,  3)),
        ),
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2,  5)),
        ),
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(   3,  5)),
        ),
    )),
    'Model_Places_Warehouse'			=> Array('chance' =>   10, 'accum' =>  15, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   8, 10)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   2,   2), 'distance' => Array(   8, 10)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',	    'num' => Array(   1,   1), 'distance' => Array(   8, 10)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2, 10)),
        ),
        Array(
            Array('type' => 'Model_Battle_Runner',	    'num' => Array(   1,   1), 'distance' => Array(  10, 15)),
        ),
    )),
    'Model_Places_Vault'		    	=> Array('chance' =>   30, 'accum' =>  30, 'range' =>   0, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(   8, 10)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   2,   6), 'distance' => Array(   8, 10)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',	    'num' => Array(   1,   1), 'distance' => Array(   8, 10)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(   2, 10)),
            Array('type' => 'Model_Battle_Runner',  	'num' => Array(   1,   1), 'distance' => Array(   8, 10)),
        ),
        Array(
            Array('type' => 'Model_Battle_Runner',	    'num' => Array(   1,   3), 'distance' => Array(  10, 15)),
        ),
    )),
    'Model_Places_Colosseum'			=> Array('chance' =>   0, 'accum' =>   0, 'range' =>   0, 'groups' => Array(
    )),
    'Model_Places_Hospital'				=> Array('chance' =>   5, 'accum' =>  30, 'range' =>  50, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Nurse',	    'num' => Array(   1,   10), 'distance' => Array(  30, 70)),
        ),
        Array(
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,    5), 'distance' => Array(  30, 70)),
        ),
        Array(
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,    5), 'distance' => Array(  30, 70)),
        ),
    )),
    'Model_Places_Hospital_Lobby'		=> Array('chance' =>  20, 'accum' => 100, 'range' =>  20, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Nurse',	    'num' => Array(   1,   10), 'distance' => Array(  30, 70)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,    5), 'distance' => Array(  30, 70)),
        ),
        Array(
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,    5), 'distance' => Array(  30, 70)),
        ),
    )),
    'Model_Places_Hospital_Korridor'	=> Array('chance' =>  10, 'accum' =>  30, 'range' =>  10, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Nurse',	    'num' => Array(   1,   10), 'distance' => Array(  30, 70)),
        ),
        Array(
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,    5), 'distance' => Array(  30, 70)),
        ),
        Array(
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,    5), 'distance' => Array(  30, 70)),
        ),
    )),
    'Model_Places_Hospital_Er'			=> Array('chance' =>  40, 'accum' => 100, 'range' =>  10, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Nurse',   	'num' => Array(  8,   20), 'distance' => Array(  30, 70)),
        ),
        Array(
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   3,   10), 'distance' => Array(  80, 90)),
        ),
        Array(
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,    5), 'distance' => Array(  20, 60)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,    5), 'distance' => Array(  20, 30)),
        ),
    )),
    'Model_Places_Hospital_Pharmacy'	=> Array('chance' =>  40, 'accum' => 150, 'range' =>  5, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,    8), 'distance' => Array(  5, 10)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,    4), 'distance' => Array(  5, 20)),
        ),
    )),
    'Model_Places_Hospital_Morgue'	    => Array('chance' =>  20, 'accum' => 0, 'range' =>  5, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Starver',		'num' => Array(   1,    10), 'distance' => Array(  5, 15)),
        ),
    )),
    'Model_Places_Hospital_Patients'	=> Array('chance' =>  10, 'accum' => 200, 'range' =>  5, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(  5,   25), 'distance' => Array(  50, 90)),
        ),
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   5), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
        Array(
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,   1), 'distance' => Array(  40, 50)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   0,   1), 'distance' => Array(  50, 60)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
        Array(
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,    5), 'distance' => Array(  20, 60)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,    5), 'distance' => Array(  20, 30)),
        ),
    )),
    'Model_Places_Hospital_Private'	    => Array('chance' =>  15, 'accum' => 90, 'range' =>  3, 'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(  3,   20), 'distance' => Array(  50, 90)),
        ),
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,  10), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   2), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
        Array(
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,   3), 'distance' => Array(  40, 50)),
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   0,   1), 'distance' => Array(  50, 60)),
            Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   4), 'distance' => Array(  50, 60)),
        ),
        Array(
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,    5), 'distance' => Array(  20, 60)),
            Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,    1), 'distance' => Array(  20, 30)),
        ),
    )),
    'Model_Places_Mall'					=> Array('chance' =>  30, 'accum' => 150, 'range' => 100, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,    2), 'distance' => Array(  10, 70)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   0,    1), 'distance' => Array(  10, 70)),
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   0,    1), 'distance' => Array(  10, 70)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,    5), 'distance' => Array(  30, 70)),
        ),
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   5,   8), 'distance' => Array(  10, 50)),
        ),
    )),
    'Model_Places_Mental'				=> Array('chance' =>   0, 'accum' =>   0, 'range' =>  0,	'groups' => Array(
    )),
    'Model_Places_Abstract_Node'		=> Array('chance' =>   /*5*/ 2, 'accum' =>   0, 'range' =>   0,	'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(    2,   4), 'distance' => Array(  20, 50)),
        ),
    )),
    'Model_Places_Pharmacy'				=> Array('chance' =>  10, 'accum' =>  20, 'range' =>  10, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   2), 'distance' => Array(   5,  10)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   2), 'distance' => Array(   5,  10)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(  10,  15)),
        ),
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(  10,  15)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   1), 'distance' => Array(  10,  15)),
        ),
        Array(
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   2), 'distance' => Array(  30,  30)),
        ),
    )),
    'Model_Places_Weaponshop'			=> Array('chance' =>  20, 'accum' =>  50, 'range' =>  15, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   5,   8)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,   1), 'distance' => Array(  10,  15)),
        ),
    )),
    'Model_Places_Store'				=> Array('chance' =>  10, 'accum' =>  70, 'range' =>   0, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   5,   8)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   2,   3)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   1,   1)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   1,   1)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   1,   1)),
        ),
    )),
    'Model_Places_Junkyard'				=> Array('chance' =>   5, 'accum' =>  30, 'range' =>  50, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   5,   8), 'distance' => Array(  10,   20)),
        ),
        Array(
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   2,   4), 'distance' => Array(  50,  80)),
        ),
        Array(
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   3,   5), 'distance' => Array(  5,   10)),
        ),
    )),
    'Model_Places_Villa'				=> Array('chance' =>  10, 'accum' =>  30, 'range' =>  15, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   0,   1)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   0,   1)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   0,   1)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,   1), 'distance' => Array(  10,  50)),
        ),
    )),
    'Model_Places_Cathedral'			=> Array('chance' =>   8, 'accum' => 100, 'range' =>  15, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(   0,  10)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   0,  10)),
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   0,   1), 'distance' => Array(   0,  10)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,   2), 'distance' => Array(  10,  50)),
        ),
    )),
    'Model_Places_Druglab'				=> Array('chance' =>  50, 'accum' => 100, 'range' =>  30, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   0,   5), 'distance' => Array(   5,   9)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   1,   2), 'distance' => Array(  10,  15)),
        ),
        Array(
                Array('type' => 'Model_Battle_Fatass',		'num' => Array(   1,   1), 'distance' => Array(  10,  30)),
                Array('type' => 'Model_Battle_Runner',		'num' => Array(   0,   3), 'distance' => Array(  10,  15)),
        ),
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   2), 'distance' => Array(   2,   4)),
        ),
    )),
    'Model_Places_Greenhouse'			=> Array('chance' =>  15, 'accum' =>  20, 'range' =>  30, 'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   1), 'distance' => Array(  0,   0)),
        ),
    )),
    'Model_Places_Mausoleum'			=> Array('chance' =>  60, 'accum' =>  40, 'range' =>  10,	'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   3), 'distance' => Array(  1,   5)),
        ),
    )),
    'Model_Places_Plant'				=> Array('chance' =>  60, 'accum' =>  10, 'range' =>  30,	'groups' => Array(
        Array(
                Array('type' => 'Model_Battle_Mutant',		'num' => Array(   0,   4), 'distance' => Array( 10,  50)),
        ),
        Array(
                Array('type' => 'Model_Battle_Mutant',		'num' => Array(   0,   2), 'distance' => Array( 10,  50)),
                Array('type' => 'Model_Battle_Mutant',		'num' => Array(   0,   2), 'distance' => Array( 10,  50)),
        ),
        Array(
                Array('type' => 'Model_Battle_Mutant',		'num' => Array(   0,   1), 'distance' => Array( 10,  50)),
                Array('type' => 'Model_Battle_Mutant',		'num' => Array(   0,   1), 'distance' => Array( 10,  50)),
                Array('type' => 'Model_Battle_Mutant',		'num' => Array(   0,   1), 'distance' => Array( 10,  50)),
                Array('type' => 'Model_Battle_Mutant',		'num' => Array(   0,   1), 'distance' => Array( 10,  50)),
        ),
    )),
    'Model_Places_Xmasfair'			    => Array('chance' =>  15, 'accum' =>  0, 'range' =>  0,	'groups' => Array(
        Array(
            Array('type' => 'Model_Battle_Shambler',	'num' => Array(   1,   2), 'distance' => Array(  1,   100)),
        ),
    )),
);