<?php defined('SYSPATH') or die('No direct access allowed.');

return [
    'submeta' => [
        '.' =>      ['name' => 'Weltkarte'],
        '..' =>     ['name' => '', 'engine' => 'Model_Map_Circular'],

        'bhouse'    => ['name' => 'Verbranntes Haus'],
        'hospital'  => [
            'name' => 'Krankenhaus',
            'engine' => 'Model_Map_Labyrinth',
            'skin' => 'hospital',
            'grid' => 5, 'size' => 50, 'distance' => 2,
            'neutral_class' => Model_Places_Hospital_Korridor::cls(), 'entry_class' => Model_Places_Hospital_Lobby::cls(),
        ],
        'camping'   => ['name' => 'Campingplatz'],
        'thouse'    => ['name' => 'Baumhaus'],
        'ashide'    => ['name' => 'Versteckter Flügel der Irrenanstalt']

    ],
    'locations' => [
        'Model_Places_Outworld'			    => Array('auto' => true, 'sub' => null, 'iteration' =>  0, 'distance' => array( 0,50), 'num' =>  1, 'max_local' =>  1, 'contortion' => -2, 'chance' =>   0, 'obvious' => true,  'branchable' => true,  'root' => null, 'fixed' => 1),
        'Model_Places_Roadtrip_Myhouse'	    => Array('auto' => true, 'sub' => null, 'iteration' =>  0, 'distance' => array( 1, 1), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   0, 'obvious' => true,  'branchable' => false, 'root' => 'Model_Places_Outworld', 'fixed' => 3),
        'Model_Places_Motorhome'			=> Array('auto' => true, 'sub' => null, 'iteration' => 20, 'distance' => array( 1, 1), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   0, 'obvious' => true,  'branchable' => false, 'root' => 'Model_Places_Outworld', 'fixed' => 2),

        'Model_Places_Plaza'                => Array('auto' => true, 'sub' => null, 'iteration' =>  1, 'distance' => array(10,15), 'num' =>  2, 'max_local' =>  1, 'contortion' => -3, 'chance' =>   3, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Roadtrip_Roadblock'   => Array('auto' => true, 'sub' => null, 'iteration' =>  1, 'distance' => array( 1,50), 'num' =>  3, 'max_local' =>  1, 'contortion' => -3, 'chance' =>   3, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        
        'Model_Places_Hospital'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array(10,20), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  3, 'chance' =>   3, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Hospital_Lobby'	    => Array('auto' => false,'sub' => 'hospital', 'num' =>   1, 'max_local' => 0, 'contortion' =>  0, 'obvious' => true, 'root' => [], 'fixed' => 1),
        'Model_Places_Hospital_Er'			=> Array('auto' => true, 'sub' => 'hospital', 'num' =>   1, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR]),
        'Model_Places_Hospital_Morgue'		=> Array('auto' => true, 'sub' => 'hospital', 'num' =>   1, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR]),
        'Model_Places_Hospital_Pharmacy'	=> Array('auto' => true, 'sub' => 'hospital', 'num' =>   1, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR,Model_Map_Labyrinth::MML_CORRIDOR]),
        'Model_Places_Hospital_Patients'	=> Array('auto' => true, 'sub' => 'hospital', 'num' =>  10, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR,Model_Map_Labyrinth::MML_CORRIDOR]),
        'Model_Places_Hospital_Private'	    => Array('auto' => true, 'sub' => 'hospital', 'num' =>   2, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR,Model_Map_Labyrinth::MML_CORRIDOR]),
        'Model_Places_Hospital_Cantina'	    => Array('auto' => true, 'sub' => 'hospital', 'num' =>   1, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR,Model_Map_Labyrinth::MML_CORRIDOR]),
        'Model_Places_Hospital_Korridor'	=> Array('auto' => false,'sub' => 'hospital', 'num' =>   0, 'max_local' => 0, 'contortion' =>  0, 'obvious' => true, 'root' => []),

        'Model_Places_Bar'			        => Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 3,10), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Burgerjoint'			=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 2, 5), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   7, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Pharmacy'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 2, 5), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   5, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Weaponshop'			=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 3,10), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   4, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Store'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 2, 5), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   6, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Outworld', 'Model_Places_Plaza')),
        'Model_Places_Petshop'				=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 1, 5), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   6, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Plaza')),
        
        'Model_Places_Constructionsite'		=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 5,15), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Plaza', 'Model_Places_Outworld')),
        'Model_Places_Diy'					=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 5,15), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Plaza', 'Model_Places_Outworld')),
        'Model_Places_Roadtrip_Garage'  	=> Array('auto' => true, 'sub' => null, 'iteration' => 10, 'distance' => array( 5,15), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   3, 'obvious' => true, 'branchable' => true,  'root' => array('Model_Places_Plaza', 'Model_Places_Outworld')),
        'Model_Places_Strangewood_Entry'	=> Array('auto' => false, 'sub' => null, 'iteration' =>  0, 'distance' => array(5,15),    'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   0, 'obvious' => false, 'branchable' => false, 'root' => 'Model_Places_Outworld'),
    ]
];