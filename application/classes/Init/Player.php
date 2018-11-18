<?php defined('SYSPATH') OR die('No direct access allowed.');

class Init_Player {

    /**
     * @param $game   Model_Game
     * @param $set    Object
     * @param $userid number
     * @param $name   string
     * @param $job    number
     * @param $level  number
     *
     * @throws Exception
     */
    public function __construct(&$game, &$set, $userid, $name, $job, $level) {
		$this->init($game, $set, $userid, $name, $job, $level);
	}

    /**
     * @param $game   Model_Game
     * @param $set    Object
     * @param $userid number
     * @param $name   string
     * @param $job    number
     * @param $level  number
     *
     * @throws Exception
     */
	private function init(&$game, &$set, $userid, $name, $job, $level): void
    {
		Globals::setPrimaryPlayer(new Model_Player($userid, $name, $set->head->mode, $job, $level));
		$player_obj = Globals::PrimaryPlayerF();
		$set->players[$userid] = $set->uin->set($player_obj);
		
		//Enter home
        $set->maps['main']->get_by_fixed_id(2)->enter();
        $player_obj->location_class($set->maps['main']->get_by_fixed_id(2)->uin());

        $init = Tool_Gamemodes::compile_startup_job($job);
        $init($game->setting_mode(), $level);

        foreach ($game->get_initialized_events() as $ev)
            $ev->event_playerCreation($player_obj);
	}
}