<?php

abstract class Model_Store_Itempack extends Model_Store_Interface {

    protected static $type = 'Item-Pakete';

    protected static $item_list;

    public static function get_description() {
        parent::get_description();
        $p = [];
        foreach (static::$item_list as $itemclass => $count)
            /** @var Model_Items_Abstract_Item $itemclass */
            $p[] = $count . ' x ' . __($itemclass::static_name());

        return '[nt]' . 'Mit diesem Paket werden folgende Gegenstände in deinem Versteck abgelegt: ' . implode(', ', $p);
    }

    public static function trigger_player_after_init(&$player) {
        parent::trigger_player_after_init($player);

        foreach (static::$item_list as $itemclass => $count)
            /** @var Model_Items_Abstract_Item $itemclass */
            for ($i = 0; $i < $count; $i++)
                $player->location()->inventory()->add(new $itemclass);
    }


}