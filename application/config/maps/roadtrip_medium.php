<?php defined('SYSPATH') or die('No direct access allowed.');

return [
    'submeta' => [
        '.' =>      ['name' => 'Weltkarte'],
        '..' =>     ['name' => '', 'engine' => 'Model_Map_Circular'],

        'bhouse'    => ['name' => 'Verbranntes Haus'],
        'hospital'  => ['name' => 'Krankenhaus'],
        'camping'   => ['name' => 'Campingplatz'],
        'thouse'    => ['name' => 'Baumhaus'],
        'ashide'    => ['name' => 'Versteckter Flügel der Irrenanstalt']

    ],
    'locations' => [
        'Model_Places_Outworld'			    => Array('auto' => true, 'sub' => null, 'iteration' =>  0, 'distance' => array( 0,50), 'num' =>  1, 'max_local' =>  1, 'contortion' => -2, 'chance' =>   0, 'obvious' => true,  'branchable' => true,  'root' => null, 'fixed' => 1),
        'Model_Places_Motorhome'			=> Array('auto' => false, 'sub' => null, 'iteration' => 20, 'distance' => array( 1, 1), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   0, 'obvious' => true,  'branchable' => false, 'root' => 'Model_Places_Outworld', 'fixed' => 2),

        'Model_Places_Plaza'                => Array('auto' => true, 'sub' => null, 'iteration' =>  1, 'distance' => array(15,30), 'num' =>  5, 'max_local' =>  2, 'contortion' => -3, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Remote'               => Array('auto' => true, 'sub' => null, 'iteration' =>  1, 'distance' => array(30,50), 'num' =>  2, 'max_local' =>  2, 'contortion' => -4, 'chance' =>   2, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Outworld'),
        'Model_Places_Roadtrip_Roadblock'   => Array('auto' => true, 'sub' => null, 'iteration' =>  1, 'distance' => array( 5, 9), 'num' =>  5, 'max_local' =>  2, 'contortion' => -3, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza', 'Model_Places_Remote')),

        'Model_Places_Burnedhouse'			=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 5,10), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   7, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Outworld'),
        'Model_Places_House_Firstfloor'		=> Array('auto' => true, 'sub' => 'bhouse', 'iteration' => 0, 'distance' => array( 0, 0), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => true,  'branchable' => true, 'root' => null, 'fixed' => 1, 'nosmartrouting' => true),
        'Model_Places_House_Cellar'		    => Array('auto' => true, 'sub' => 'bhouse', 'iteration' => 0, 'distance' => array( 1, 2), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => false, 'branchable' => true, 'root' => 'Model_Places_House_Firstfloor'),
        'Model_Places_House_Hobby'		    => Array('auto' => true, 'sub' => 'bhouse', 'iteration' => 0, 'distance' => array( 2, 5), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => false, 'branchable' => true, 'root' => 'Model_Places_House_Cellar'),
        'Model_Places_House_Secondfloor'	=> Array('auto' => true, 'sub' => 'bhouse', 'iteration' => 0, 'distance' => array( 2 ,3), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => false, 'branchable' => true, 'root' => 'Model_Places_House_Firstfloor'),

        'Model_Places_Treehouse'            => Array('auto' => true, 'sub' => 'thouse', 'iteration' => 0, 'distance' => array( 0, 0), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => true,  'branchable' => true, 'root' => null, 'fixed' => 1),

        'Model_Places_Hospital'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array(30,50), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  3, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Outworld'),
        'Model_Places_Hospital_Lobby'	    => Array('auto' => true, 'sub' => 'hospital', 'iteration' => 0, 'distance' => array( 0, 0), 'num' =>   1, 'max_local' =>   1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => true, 'branchable' => true, 'root' => null, 'fixed' => 1),
        'Model_Places_Hospital_Er'			=> Array('auto' => true, 'sub' => 'hospital', 'iteration' => 0, 'distance' => array(10,15), 'num' =>   1, 'max_local' =>   1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => true, 'branchable' => true, 'root' => 'Model_Places_Hospital_Lobby'),
        'Model_Places_Hospital_Morgue'		=> Array('auto' => true, 'sub' => 'hospital', 'iteration' => 0, 'distance' => array(10,15), 'num' =>   1, 'max_local' =>   1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true, 'root' => 'Model_Places_Hospital_Lobby'),
        'Model_Places_Hospital_Pharmacy'	=> Array('auto' => true, 'sub' => 'hospital', 'iteration' => 0, 'distance' => array( 5,10), 'num' =>   1, 'max_local' =>   1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true, 'root' => 'Model_Places_Hospital_Lobby'),
        'Model_Places_Hospital_Patients'	=> Array('auto' => true, 'sub' => 'hospital', 'iteration' => 0, 'distance' => array( 3,5), 'num' =>  20, 'max_local' =>  10, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true, 'root' => 'Model_Places_Hospital_Korridor'),
        'Model_Places_Hospital_Private'	    => Array('auto' => true, 'sub' => 'hospital', 'iteration' => 0, 'distance' => array( 3,6), 'num' =>   3, 'max_local' =>   1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true, 'root' => 'Model_Places_Hospital_Korridor'),
        'Model_Places_Hospital_Korridor'	=> Array('auto' => true, 'sub' => 'hospital', 'iteration' => 0, 'distance' => array( 5,10), 'num' =>  10, 'max_local' =>   2, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true, 'root' => array('Model_Places_Hospital_Lobby','Model_Places_Hospital_Korridor')),

        'Model_Places_Bar'			        => Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 3,10), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Burgerjoint'			=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 2, 5), 'num' =>  2, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   7, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza'), 'force_root' => array('Model_Places_Outworld')),
        'Model_Places_Pharmacy'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 2, 5), 'num' =>  2, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   5, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza'), 'force_root' => array('Model_Places_Outworld')),
        'Model_Places_Weaponshop'			=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array(10,15), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   4, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Store'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 3,10), 'num' =>  2, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   6, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Roadtrip_Garage'  	=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 5,15), 'num' =>  3, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Plaza', 'Model_Places_Outworld', 'Model_Places_Remote')),

        'Model_Places_House'		        => Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 6,10), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Plaza', 'Model_Places_Remote')),
        'Model_Places_Hotel'		        => Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 3, 8), 'num' =>  2, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   5, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Plaza')),
        'Model_Places_Warehouse'		    => Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 5,10), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   5, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Plaza', 'Model_Places_Remote')),
        'Model_Places_Vault'		        => Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 5,15), 'num' =>  3, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   1, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Remote')),

        'Model_Places_Constructionsite'		=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 1, 6), 'num' =>  3, 'max_local' =>  3, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Plaza', 'Model_Places_Remote')),
        'Model_Places_Diy'					=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 5,15), 'num' =>  3, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Plaza', 'Model_Places_Remote')),
        'Model_Places_Mall'					=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array(10,40), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Remote'),
        'Model_Places_Mental'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array(15,35), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Outworld'),
        'Model_Places_Junkyard'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 5,15), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   5, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Remote')),
        'Model_Places_Druglab'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 3, 6), 'num' =>  2, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Remote'),
        'Model_Places_Radio'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array(10,15), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   5, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Remote'),
        'Model_Places_Greenhouse'			=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array(30,50), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Outworld'),
        'Model_Places_Mausoleum'			=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array(10,15), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   1, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Outworld'),
        'Model_Places_Plant'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array(10,40), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Outworld'),

        'Model_Places_Camping'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array(10,20), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => false, 'branchable' => true,  'root' => 'Model_Places_Remote'),
        'Model_Places_Camping_Office'	    => Array('auto' => true, 'sub' => 'camping', 'iteration' => 0, 'distance' => array( 0, 0), 'num' =>   1, 'max_local' =>   1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => true, 'branchable' => true, 'root' => null, 'fixed' => 1),
        'Model_Places_Camping_Grill'	    => Array('auto' => true, 'sub' => 'camping', 'iteration' => 0, 'distance' => array( 5,15), 'num' =>  3, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true, 'root' => array('Model_Places_Camping_Office','Model_Places_Camping_Grill')),
        'Model_Places_Camping_Caravan'	    => Array('auto' => true, 'sub' => 'camping', 'iteration' => 1, 'distance' => array( 2,20), 'num' =>  2, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   20, 'obvious' => false, 'branchable' => true, 'root' => array('Model_Places_Camping_Office','Model_Places_Camping_Grill')),
        'Model_Places_Camping_Tent'	        => Array('auto' => true, 'sub' => 'camping', 'iteration' => 1, 'distance' => array( 2,20), 'num' =>  25, 'max_local' =>  10, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true, 'root' => array('Model_Places_Camping_Grill')),

        'Model_Places_Toilet'				=> Array('auto' => true, 'sub' => array(null,'camping'), 'iteration' => 10, 'distance' => array(5,10), 'num' =>  5, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Outworld','Model_Places_Remote','Model_Places_Plaza','Model_Places_Camping_Office','Model_Places_Camping_Grill')),

        'Model_Places_Asylumhideout'        => Array('auto' => true, 'sub' => 'ashide', 'iteration' => 0, 'distance' => array( 0, 0), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => true,  'branchable' => true, 'root' => null, 'fixed' => 1),
    ]
];