<?php

class Model_Store_HeroicArchitective extends Model_Store_Heroic {

    protected static $icon = 'hero_architective';

    protected static $cost = 500;

    protected static function get_heroic_item_class(): string {
        return Model_Items_Virtual_Hero_ShopArchitective::cls();
    }

}