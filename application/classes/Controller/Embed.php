<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Embed extends Controller {

    protected static $force_ajax = false;

    public function before() {
        //Load session, perform session checks
        $this->session = Session::instance();
    }

    public function japi_battle() {
        /** @global Model_Game|null $game */
        global $game;

        $video_id = (int)$this->request->post('v');
        $gallery_id = (int)$this->request->post('g');

        if (!$video_id || !($gallery_id || ($chk = Model_Combat_Handler::check_battle($video_id))) || (!$game && !$gallery_id) || (!$gallery_id && $game->id() != $chk))
            return $this->render(['video' => null]);

        return $this->render(['video' => $gallery_id ? Model_Combat_Handler::get_battle_from_gallery($video_id,$gallery_id) : Model_Combat_Handler::get_battle($video_id)]);
    }

    public function action_battle() {
        $video_id = (int)$this->request->query('v');
        $gallery_id = (int)$this->request->query('p');

        $this->response->body(
            View::factory('battle')
                ->set('path', URL::base(null, true))
                ->set('pid', $gallery_id)
                ->set('bid', $video_id)
        );
    }
}