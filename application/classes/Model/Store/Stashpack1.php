<?php

class Model_Store_Stashpack1 extends Model_Store_Itempack {

    protected static $cost = 30;
    protected static $name = 'Geheimnisvolle Kisten 1';
    protected static $icon = 'stashpack1';

    protected static $description = 'Was in diesen Kisten steckt? Keine Ahnung! Finde es doch selbst heraus, wenn du dich traust (und es dir leisten kannst).';

    protected static $item_list = ['Model_Items_Stash2' => 3];

}