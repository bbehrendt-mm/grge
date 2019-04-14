<?php

class Model_Store_Bikepack2 extends Model_Store_Itempack {

    protected static $cost = 180;
    protected static $name = 'Tour de France Paket';
    protected static $icon = 'bikepack2';

    protected static $description = 'Fahrrad fahren kann jeder Idiot - aber nicht jeder hat das Zeug, ein wahrer Radprofi zu werden. Und mit "Zeug" ist in diesem Fall natürlich ein genau abgestimmter Medikamente-Cocktail gemeint.';

    protected static $item_list = [
        'Model_Items_Generic_Bike2' => 1,
        'Model_Items_Steroids' => 5
    ];

}