<?php

class Model_Store_HeroicEagleEye extends Model_Store_Heroic {

    protected static $icon = 'hero_eagleeye';

    protected static function get_heroic_item_class(): string {
        return Model_Items_Virtual_Hero_ShopEagleEye::cls();
    }

}