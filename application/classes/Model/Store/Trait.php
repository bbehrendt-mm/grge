<?php

abstract class Model_Store_Trait extends Model_Store_Interface {

    protected static $type = 'Fähigkeiten';
    protected static $buff;

    public static function get_name(): string
    {
        $b = static::$buff;
        /** @var Model_Buffs_Abstract_Buff $b */
        return '[nt]' . __('Fähigkeit ":trait"', [':trait' => __($b::static_name())]);
    }

    public static function get_description(): string
    {
        $b = static::$buff;
        /** @var Model_Buffs_Abstract_Buff $b */
        return $b::static_description();
    }

    public static function trigger_player_after_init($player): void
    {
        parent::trigger_player_after_init($player);

        $b = static::$buff;
        /** @var Model_Buffs_Abstract_Buff $b */

        new $b($player->id());
    }


}