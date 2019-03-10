<?php

abstract class Model_Store_Heroic extends Model_Store_Itempack {

    protected static $type = 'Heldentaten';
    protected static $personal = true;

    protected static $cost = 100;

    abstract protected static function get_heroic_item_class(): string;

    public static function get_name(): string {
        /** @var Model_Items_Abstract_Virtual $cls */
        $cls = static::get_heroic_item_class();
        return '[nt]' . __('Käufliche Heldentat') . ': ' . __($cls::static_name());
    }

    public static function get_description(): string {
        /** @var Model_Items_Abstract_Virtual $cls */
        $cls = static::get_heroic_item_class();
        return '[nt]' . __('Dieses Paket ermöglicht dir den Einsatz einer neuen Heldentat.') . ' ' . $cls::static_description();
    }

    protected static function get_item_instances(): array {
        return [[
            'i' => static::get_heroic_item_class(),
            'c' => 1
        ]];
    }
}