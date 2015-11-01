<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle extends Model_Log_Message {

    protected static $type = Model_Log_Message::MLM_COMBAT;

	public function __construct($title = 'Ein Kampf!', $text = null, $video_id) {
        /** @global Model_Player $player */
        global $player;

        parent::__construct([
            'bid' => $video_id,
            'msg' => $title,
            'bdy' => $text
        ]);
	}

    protected function postprocess($data) {
        $data['msg'] = __($data['msg']);
        if (isset($data['bdy']) && $data['bdy'])
            $data['bdy'] = __($data['bdy']);
        else unset($data['bdy']);

        return $data;
    }
}