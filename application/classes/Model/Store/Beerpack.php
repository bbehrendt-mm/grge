<?php

class Model_Store_Beerpack extends Model_Store_Itempack {

    protected static $cost = 25;
    protected static $name = 'Feierabend-Bier';
    protected static $icon = 'beerpack';

    protected static $description = 'So ein ZombVival-Spiel zu starten ist schon ziemlich harte Arbeit... Belohne dich selbst, indem du dir ein Feierabend-Bier genehmigst - oder direkt mehrere!';

    protected static $item_list = ['Model_Items_Beer' => 3];

}