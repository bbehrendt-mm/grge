<?php

class Model_Store_Metalpack extends Model_Store_Itempack {

    protected static $cost = 50;
    protected static $name = 'Schwermetall-Paket';
    protected static $icon = 'metalpack';

    protected static $item_list = ['Model_Items_Generic_Metal' => 5, 'Model_Items_Generic_Crmetal' => 5];

}