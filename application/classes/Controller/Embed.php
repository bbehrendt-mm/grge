<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Embed extends Controller {

    protected static $force_ajax = false;

    public function before() {
        //Load session, perform session checks
        $this->session = Session::instance();
    }

    public function japi_battle() {
        $video_id = $this->request->post('v');

        if (!$video_id)
            return $this->render(['video' => null]);

        $data = DB::select('data')->from('battle')->where('bid','=',$video_id)->execute()->get('data');
        if ($data)
            $data = unserialize(gzuncompress($data));

        if (!$data)
            return $this->render(['video' => null]);

        return $this->render(['video' => $data]);
    }

    public function action_battle() {
        $video_id = $this->request->query('v');

        $this->response->body(
            View::factory('battle')
                ->set('path', URL::base(null, true))
                ->set('pid', null)
                ->set('bid', $video_id)
        );
    }
}