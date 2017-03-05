<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Game extends Controller_Admin_Admin {

    protected static $auto_require = ['GAMELIST'];
    
    public function action_info() {

        $game_id = (int)$this->request->param('id', 0);
        if ($game_id == 0) return $this->not_found();

        $local_game_obj = new Model_Game();
        if (!$local_game_obj->read($game_id, false))
            return $this->not_found();


        $this->add_widget(View::factory('admin/game')
            ->set('id', $game_id)
            ->set('name',$local_game_obj->name())
            ->set('mode', Tool_Gamemodes::get_board_by_id($local_game_obj->setting_mode()))
            ->set('duration', Tool_Numerics::duration_to_string($local_game_obj->duration()))
            ->render());

        return $this->render();
    }
}