<?php

class Model_Store_Stashpack2 extends Model_Store_Itempack {

    protected static $cost = 120;
    protected static $name = 'Geheimnisvolle Kisten 2';
    protected static $icon = 'stashpack2';

    protected static $description = 'Was in diesen Kisten steckt? Keine Ahnung! Finde es doch selbst heraus, wenn du dich traust (und es dir leisten kannst).';

    protected static $item_list = ['Model_Items_Stash3' => 3];

}