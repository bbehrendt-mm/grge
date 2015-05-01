<?php

abstract class Model_Store_Itempack extends Model_Store_Interface {

    protected static $type = 'Item-Pakete';
    protected static $personal = false;

    protected static $item_list = [];

    public static function is_valid_for($mode,$job,$init,$id,$flow) {
        return (static::$personal || $init) && parent::is_valid_for($mode,$job,$init,$id,$flow);
    }

    protected static function get_item_instances() {
        $r = [];
        foreach (static::$item_list as $itemclass => $count)
            $r[] = [
                'i' => $itemclass,
                'c' => $count
            ];
        return $r;
    }

    public static function get_description() {
        parent::get_description();
        $p = [];
        foreach (static::get_item_instances() as $instance)
            $p[] = ((!is_object($instance['i']) || $instance['i']->count() === null) ? $instance['c'] : $instance['i']->count()) . ' x ' . __($instance['i']::static_name());

        return '[nt]' . __(parent::get_description()) . ' ' . (static::$personal ? __('Mit diesem Paket werden folgende Gegenstände in deinem Inventar abgelegt:') : __('Mit diesem Paket werden folgende Gegenstände in deinem Versteck abgelegt:')) . ' ' . implode(', ', $p);
    }

    public static function trigger_player_after_init(&$player) {
        parent::trigger_player_after_init($player);

        foreach (static::get_item_instances() as $instance) {
            $items = [];
            if (is_object($instance['i'])) $items = [$instance['i']];
            else for ($i = 0; $i < $instance['c']; $i++) $items[] = new $instance['i'];

            /** @var Model_Items_Abstract_Item[] $items */
            foreach ($items as $item)
                if (static::$personal && Tool_System::instance_of($item, 'Model_Items_Abstract_Ammo') && ($belt = Tool_Scripts::first_available_item('Model_Items_Ammobelt')))
                    $belt->add($item);
                elseif (static::$personal && !Tool_System::instance_of($item, 'Model_Items_Abstract_Ammo') && $player->inventory()->add($item)) {}
                else $player->location()->inventory()->add($item);
        }
    }


}