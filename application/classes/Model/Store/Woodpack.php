<?php

class Model_Store_Woodpack extends Model_Store_Itempack {

    protected static $cost = 50;
    protected static $name = 'Morgenlatte-Paket';
    protected static $icon = 'woodpack';

    protected static $item_list = ['Model_Items_Generic_Wood' => 5, 'Model_Items_Generic_Crwood' => 5];

}