<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
    'groups' => array(
        'xmas_generic' => array(
                'Model_Items_Generic_Spice'     =>      1,
                'Model_Items_Generic_Bobblehead'=>      1,
                'Model_Items_Generic_Cloth'     =>      1,
                'Model_Items_Generic_Electro'   =>      1,
                'Model_Items_Generic_Xmasneedles'=>     1,
                'Model_Items_Generic_Ducttape'  =>      1,
                'Model_Items_Stick'             =>      1,
                'Model_Items_Generic_Cd'        =>      5,
                'Model_Items_Generic_Led'       =>      5,
                'Model_Items_Generic_Bauble'    =>      5,
                'Model_Items_Generic_Wire'      =>      2,
        ),
        'xmas_food' => array(
            'Model_Items_Xmas_Cookie'   =>      2,
            'Model_Items_Xmas_Meat'     =>      1,
            'Model_Items_Xmas_Sweets'   =>      1,
        ),
        'xmas_drink' => array(
            'Model_Items_Xmas_Wine'     =>      2,
            'Model_Items_Xmas_Beer'     =>      1,
            'Model_Items_Xmas_Drink'    =>      1,
            'Model_Items_Xmas_Coffee'    =>      1,
        ),
    ),
    'buildings' => array(
        'Model_Places_Xmasfair'				=> Array('size' =>  5,
            'content' => Array('xmas_generic' => 1, 'xmas_food' => 1, 'xmas_drink' => 1),
        ),
        'Model_Places_Xmas_Deco'			=> Array('size' =>  5,
            'content' => Array('xmas_generic' => 5, 'xmas_food' => 1, 'xmas_drink' => 1),
        ),
        'Model_Places_Xmas_Food'				=> Array('size' =>  5,
            'content' => Array('xmas_generic' => 1, 'xmas_food' => 5, 'xmas_drink' => 1),
        ),
        'Model_Places_Xmas_Drink'				=> Array('size' =>  5,
            'content' => Array('xmas_generic' => 1, 'xmas_food' => 1, 'xmas_drink' => 5),
        ),
    ),
);