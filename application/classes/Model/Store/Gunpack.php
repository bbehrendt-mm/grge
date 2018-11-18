<?php

class Model_Store_Gunpack extends Model_Store_Itempack {

    protected static $cost = 150;
    protected static $name = 'NRA-Waffenlizenz';
    protected static $icon = 'nra';
    protected static $personal = true;

    protected static $description = 'Die NRA-Waffenlizenz wird nur den ehrbarsten Bürger der Vereinigten Staaten verliehen; außerdem Drogendealern, Tierquälern, gewalttätigen Schwerverbrechern, Vergewaltigern, Drogenabhängigen, Terroristen, psychisch gestörte Veteranen und Politikern.';

    protected static function get_item_instances(): array
    {
        return [['i' => 'Model_Items_Nrarifle','c' => 1],['i' => new Model_Items_Ammo(15),'c' => 1]];
    }

}