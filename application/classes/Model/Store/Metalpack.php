<?php

class Model_Store_Metalpack extends Model_Store_Itempack {

    protected static $cost = 50;
    protected static $name = 'Schwermetall-Paket';
    protected static $icon = 'metalpack';

    protected static $description = 'Wer auf alles vorbereitet sein will, der sollte immer einen Vorrat an Baumaterialien bereit halten. Leider warst du richtig scheiße auf die Zombieapokalypse vorbereitet, daher musst du dein Baumaterial jetzt hier kaufen.';

    protected static $item_list = ['Model_Items_Generic_Metal' => 5, 'Model_Items_Generic_Crmetal' => 5];

}