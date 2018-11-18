<?php

class Model_Store_Oneup extends Model_Store_Interface {

    protected static $cost = 200;
    protected static $name = 'Level +1';
    protected static $description = 'Dieses Paket erhöht deinen Berufslevel ausschließlich für das aktuelle Spiel um 1. Dir stehen also alle Boni des nächsten Berufslevels zur Verfügung.';
    protected static $icon = '1up';
    protected static $type = 'Spielmodifikatoren';

    public static function is_valid_for($mode,$job,$init,$id,$flow): bool
    {
        //Get config
        $config = Tool_Gamemodes::compile_mode_database(true);

        //Check if mode is valid
        if (!isset($config['modes'][$mode]) || $config['modes'][$mode]['locked'])
            return false;

        //Check if job is valid
        if (!isset($config['jobs'][$job])
            || $config['jobs'][$job]['locked']
            || !in_array($job, $config['modes'][$mode]['jobs'], true)
        )
            return false;

        return ($config['jobs'][$job]['next_level'] && $config['jobs'][$job]['level'] < (count($config['jobs'][$job]['levels']) + 1));
    }

    /**
     * @param int $job
     * @param int $level
     */
    public static function trigger_player_before_init(&$job, &$level): void
    {
        $level = min($level, count(Tool_Gamemodes::compile_mode_database(true)['jobs'][$job]['levels'])) + 1;
    }
}