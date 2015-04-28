<?php

abstract class Model_Store_Interface extends Model {

    protected static $cost;
    protected static $name;
    protected static $description;
    protected static $icon;
    protected static $type;

    public static function get_cost() {return static::$cost;}
    public static function get_name() {return static::$name;}
    public static function get_description() {return static::$description;}
    public static function get_icon() {return static::$icon;}
    public static function get_type() {return static::$type;}

    /**
     * @param int $job
     * @param int $level
     */
    public static function trigger_player_before_init(&$job, &$level) {}

    /**
     * @param Model_Player $player
     */
    public static function trigger_player_after_init(&$player) {}

    /**
     * @param Model_Game $game
     */
    public static function trigger_game_after_init(&$game) {}

}