<?php

class Model_Store_Maxup extends Model_Store_Interface {

    protected static $cost = 1000;
    protected static $name = 'Max. Level';
    protected static $description = 'Dieses Paket erhöht deinen Berufslevel ausschließlich für das aktuelle Spiel auf den Maximalwert. Selbst hochleveln? Pff, das ist für arme Leute!';
    protected static $icon = 'Mup';
    protected static $type = 'Spielmodifikatoren';

    public static function is_valid_for($mode,$job,$init,$id,$flow) {
        //Get config
        $config = Tool_Gamemodes::compile_mode_database(true);

        //Check if mode is valid
        if (!isset($config['modes'][$mode]) || $config['modes'][$mode]['locked'])
            return false;

        //Check if job is valid
        if (!in_array($job, $config['modes'][$mode]['jobs']) || !isset($config['jobs'][$job]) || $config['jobs'][$job]['locked'])
            return false;

        return ($config['jobs'][$job]['next_level'] && $config['jobs'][$job]['level'] < (count($config['jobs'][$job]['levels']) + 2));
    }

    /**
     * @param int $job
     * @param int $level
     */
    public static function trigger_player_before_init(&$job, &$level) {
        $level = count(Tool_Gamemodes::compile_mode_database(true)['jobs'][$job]['levels']) + 1;
    }
}