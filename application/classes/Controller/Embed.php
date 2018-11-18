<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Embed extends Controller {

    protected static $force_ajax = false;

    public function before(): void {
        //Load session, perform session checks
        $this->session = Session::instance();
    }

    public function japi_battle(): bool
    {
        $video_id = (int)self::post('v');
        $gallery_id = (int)self::post('g');

        if (!$video_id || !($gallery_id || ($chk = Model_Combat_Handler::check_battle($video_id))) || (!Globals::hasCurrentGame() && !$gallery_id) || (!$gallery_id && Globals::CurrentGameF()->id() !== $chk))
            return $this->render(['video' => null]);

        return $this->render(['video' => $gallery_id ? Model_Combat_Handler::get_battle_from_gallery($video_id,$gallery_id) : Model_Combat_Handler::get_battle($video_id)]);
    }

    public function action_battle(): void
    {
        $video_id = (int)$this->request->param('v', $this->request->query('v'));
        $gallery_id = (int)$this->request->param('p', $this->request->query('p'));

        $this->response->body(
            View::factory('battle')
                ->set('path', URL::base(null, true))
                ->set('lang', I18n::lang())
                ->set('pid', $gallery_id)
                ->set('bid', $video_id)
                ->set('url', URL::base(true))
        );
    }
}