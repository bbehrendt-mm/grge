<?php

class Model_Store_Investpack extends Model_Store_Itempack {

    protected static $cost = 25;
    protected static $name = 'Investitions-Paket';
    protected static $icon = 'investpack';

    protected static $description = 'Willst du ein bisschen Extrakohle machen? Dann kaufe dieses Investitions-Paket (vorzugsweise von deinem Freibetrag) und erhalte direkt beim Spielstart BrainCoins!';

    protected static function get_item_instances(): array
    {
        return [['i' => new Model_Items_Braincoin(10),'c' => 1]];
    }

}