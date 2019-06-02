<?php defined('SYSPATH') OR die('No direct access allowed.');

class Init_Game {

	public function __construct(&$game, &$set, $gameid, $mode, $flow, $speed, $contest = null, $name = null) {
		$this->init($game, $set, $gameid, $mode, $flow, $speed, $contest, $name);
	}
	
	private function init(Model_Game $game, &$set, $gameid, $mode, $flow, $speed, $contest = null, $name = null): void
    {
        if (!($config_data = Tool_Gamemodes::compile_startup_mode($mode)))
            throw new RuntimeException('Unable to compile game setup configuration!');

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
		$set->head->params = [];
		$set->head->paused = false;
		$set->head->pauselock = 0;
        $set->head->name = $name;
        $set->head->daytime_offset = random_int(
            $config_data['config']['game.bhav.time_offset_range'][0],
            $config_data['config']['game.bhav.time_offset_range'][1]
        );

        //Config
        foreach ($config_data['config'] as $key => $value)
            $game->config($key, $value);

        //NDP Storage
        $set->ndp = [];
		
		//Contest info
		$set->head->contest = Model_Game::get_contest_data($contest);
		
		//UIN Init'ed
		$set->uin = new Model_Uinmanager($gameid);

        //Players
        $set->players = [];
        $set->npcs = [];
        $set->graveyard = [];
        $set->ghuls = [];

        //Counters
        $set->counters = [];

		//Map
        $map = Model_Map_Abstract::factory($config_data['config']['game.config.map']);
        $map->auto_init();
		$set->maps['main'] = $map;

		//Generic Props
        $set->props = [];
	}
}