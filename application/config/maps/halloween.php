<?php defined('SYSPATH') or die('No direct access allowed.');

return [
    'submeta' => [
        '..' => [
            'name' => 'Strangewood',
            'engine' => 'Model_Map_Labyrinth',
            'skin' => 'swood',
            'grid' => 10, 'size' => 150, 'distance' => 2,
            'neutral_class' => Model_Places_Strangewood_Path::cls(), 'entry_class' => Model_Places_Strangewood_Exit::cls(),
        ],
    ],
    'locations' => [
        Model_Places_Strangewood_Exit::cls()   => Array('auto' => false,'sub' => 'swood', 'num' =>   1, 'max_local' => 0, 'contortion' =>  0, 'obvious' => true, 'root' => [], 'fixed' => 1),
        Model_Places_Strangewood_Clearing::cls()  => Array('auto' => true, 'sub' => 'swood', 'num' =>  25, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_INTERSECTION]),
        Model_Places_Strangewood_Forrester::cls() => Array('auto' => true, 'sub' => 'swood', 'num' =>   1, 'max_local' => 1, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_CORRIDOR]),
        Model_Places_Strangewood_Path::cls()	=> Array('auto' => false,'sub' => 'swood', 'num' =>   0, 'max_local' => 0, 'contortion' =>  0, 'obvious' => true, 'root' => []),

        Model_Places_Strangewood_Singleplayer::cls() => Array('auto' => false,'sub' => 'swood', 'num' =>   0, 'max_local' => 0, 'contortion' =>  0, 'obvious' => true, 'root' => [Model_Map_Labyrinth::MML_FAR]),
    ]
];