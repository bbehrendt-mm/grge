<?php

class Model_Store_Bikepack extends Model_Store_Itempack {

    protected static $cost = 75;
    protected static $name = 'Spritztour-Paket';
    protected static $icon = 'bikepack';

    protected static $description = 'Es gibt keine bessere Art, von A nach B zu kommen, als mit dem Fahrrad - wenn man mal von all den Arten absieht, die besser sind als ein Fahrrad. Apropos Fahrrad: Wie wärs, wenn du hier etwas Geld für ein Item ausgeben würdest, dass du ohnehin im Spiel finden kannst?';

    protected static $item_list = ['Model_Items_Generic_Bike2' => 1];

}