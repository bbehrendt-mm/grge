<?php defined('SYSPATH') OR die('No direct access allowed.');

class Init_Game {

	public function __construct(&$game, &$set, $gameid, $mode, $flow, $speed, $contest = null, $name = null) {
		$this->init($game, $set, $gameid, $mode, $flow, $speed, $contest, $name);
	}
	
	private function init(&$game, &$set, $gameid, $mode, $flow, $speed, $contest = null, $name = null) {
        if (!($config_data = Tool_Gamemodes::compile_startup_mode($mode)))
            throw new Exception('Unable to compile game setup configuration!');

		//Base container
		$set = new stdClass;
		
		//Time control
		$set->timing = new stdClass;
		$set->timing->game_start = time();
		$set->timing->last_point = time();
		$set->timing->tick_lenght = $speed;
		$set->timing->flow_mode = $flow;
		
		//Head information
		$set->head = new stdClass;
		$set->head->mode = $mode;
		$set->head->rankable = true;
		$set->head->season = Kohana::$config->load('server.season');
		$set->head->next_uin = 1;
		$set->head->ticks = 0;
		$set->head->params = Array();
		$set->head->paused = false;
		$set->head->pauselock = 0;
        $set->head->name = $name;
        $set->head->daytime_offset = mt_rand(60,216);

        //Config
        foreach ($config_data['config'] as $key => $value)
            $game->config($key, $value);

        //NDP Storage
        $set->ndp = array();
		
		//Contest info
		$set->head->contest = Model_Game::get_contest_data($contest);
		
		//UIN Inted
		$set->uin = new Model_Uinmanager($gameid);

        //Players
        $set->players = Array();
        $set->graveyard = Array();
        $set->ghuls = Array();

		//Map
		$set->maps['main'] = new Model_Map($config_data['config']['game.config.map']);
        $set->maps['main']->auto_init();

		//Log
		$set->logstore = new Model_Log;
	}
}