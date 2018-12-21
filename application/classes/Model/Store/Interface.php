<?php

abstract class Model_Store_Interface extends Model {

    protected static $cost;
    protected static $name;
    protected static $description;
    protected static $icon;
    protected static $type;

    public static function get_cost() {return static::$cost;}
    public static function get_name(): string
    {return static::$name;}
    public static function get_description(): string
    {return static::$description;}
    public static function get_icon() {return static::$icon;}
    public static function get_type() {return static::$type;}
    public static function is_valid_for($mode,$job,$init,$id,$flow): bool
    {return true;}

    /**
     * @param int $job
     * @param int $level
     */
    public static function trigger_player_before_init(&$job, &$level): void
    {}

    /**
     * @param Model_Player $player
     */
    public static function trigger_player_after_init(Model_Player $player): void
    {}

    /**
     * @param Model_Game $game
     */
    public static function trigger_game_after_init($game): void
    {}

}