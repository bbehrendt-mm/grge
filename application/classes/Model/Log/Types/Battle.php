<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle extends Model_Log_Message {

    protected static $type = Model_Log_Message::MLM_COMBAT;

	public function __construct($message = 'Ein Kampf!', $video_id) {
        /** @global Model_Player $player */
        global $player;

        parent::__construct([
            'bid' => $video_id,
            'msg' => $message,
        ]);
	}

    protected function postprocess($data) {
        $data['msg'] = __($data['msg']);

        return $data;
    }
}