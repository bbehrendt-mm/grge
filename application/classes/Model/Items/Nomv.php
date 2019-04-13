<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Nomv extends Model_Items_Nom2 {

    protected static $instances_info = Array(
        [
            'name'          => 'Spätzle',
            'icon'          => 'customfood/01',
        ],
        [
            'name'          => 'Sandwich',
            'icon'          => 'customfood/02',
        ],
        [
            'name'          => 'Bunter Teller',
            'icon'          => 'customfood/03',
        ],
        [
            'name'          => 'Omlette mit Speck',
            'icon'          => 'customfood/04',
        ],
    );

    protected static $hunger = 75;
    protected static $buff_duration = 72;



}	