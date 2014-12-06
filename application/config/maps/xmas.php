<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
    'Model_Places_Xmasfair'            => Array('auto' => true, 'sub' => 'xmas', 'iteration' => 0, 'distance' => array( 0, 0), 'num' =>  1, 'max_local' =>  1, 'contortion' =>  0, 'chance' =>   2, 'obvious' => true,  'branchable' => true, 'root' => null, 'fixed' => 1),

    'Model_Places_Xmas_Deco'           => Array('auto' => true, 'sub' => 'xmas', 'iteration' =>  1, 'distance' => array(5,50), 'num' =>  20, 'max_local' =>  5, 'contortion' => -3, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Xmasfair','Model_Places_Xmas_Food','Model_Places_Xmas_Deco','Model_Places_Xmas_Drink')),
    'Model_Places_Xmas_Food'           => Array('auto' => true, 'sub' => 'xmas', 'iteration' =>  1, 'distance' => array(5,50), 'num' =>  8, 'max_local' =>  3, 'contortion' => -3, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Xmasfair','Model_Places_Xmas_Food','Model_Places_Xmas_Deco','Model_Places_Xmas_Drink')),
    'Model_Places_Xmas_Drink'          => Array('auto' => true, 'sub' => 'xmas', 'iteration' =>  1, 'distance' => array(5,50), 'num' =>  8, 'max_local' =>  3, 'contortion' => -3, 'chance' =>   3, 'obvious' => false, 'branchable' => true,  'root' => array('Model_Places_Xmasfair','Model_Places_Xmas_Food','Model_Places_Xmas_Deco','Model_Places_Xmas_Drink')),
);