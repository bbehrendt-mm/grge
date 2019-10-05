<?php defined('SYSPATH') or die('No direct access allowed.');

return [
    'submeta' => [
        '..' => [
            'name' => 'Strangewood',
            'engine' => 'Model_Map_Labyrinth',
            'skin' => 'swood',
            'grid' => 6, 'size' => 200, 'distance' => 2,
            'neutral_class' => Model_Places_Strangewood_Path::cls(), 'entry_class' => Model_Places_Strangewood_Exit::cls(),
        ],
    ],
    'locations' => [
        Model_Places_Strangewood_Exit::cls()   => Array('auto' => false,'sub' => 'swood', 'num' =>   1, 'max_local' => 0, 'contortion' =>  0, 'obvious' => true, 'root' => [], 'fixed' => 1),
        //'Model_Places_Hospital_Er'			=> Array('auto' => true, 'sub' => 'hospital', 'num' =>   1, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR]),
        //'Model_Places_Hospital_Morgue'		=> Array('auto' => true, 'sub' => 'hospital', 'num' =>   1, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR]),
        //'Model_Places_Hospital_Pharmacy'	=> Array('auto' => true, 'sub' => 'hospital', 'num' =>   1, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR,Model_Map_Labyrinth::MML_CORRIDOR]),
        //'Model_Places_Hospital_Patients'	=> Array('auto' => true, 'sub' => 'hospital', 'num' =>  20, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR,Model_Map_Labyrinth::MML_CORRIDOR]),
        //'Model_Places_Hospital_Private'	    => Array('auto' => true, 'sub' => 'hospital', 'num' =>   4, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR,Model_Map_Labyrinth::MML_CORRIDOR]),
        //'Model_Places_Hospital_Cantina'	    => Array('auto' => true, 'sub' => 'hospital', 'num' =>   2, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR,Model_Map_Labyrinth::MML_CORRIDOR]),
        Model_Places_Strangewood_Path::cls()	=> Array('auto' => false,'sub' => 'swood', 'num' =>   0, 'max_local' => 0, 'contortion' =>  0, 'obvious' => true, 'root' => []),

    ]
];