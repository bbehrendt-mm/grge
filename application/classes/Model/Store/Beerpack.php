<?php

class Model_Store_Beerpack extends Model_Store_Itempack {

    protected static $cost = 25;
    protected static $name = 'Feierabend-Bier';
    protected static $icon = 'beerpack';

    protected static $item_list = ['Model_Items_Beer' => 3];

}