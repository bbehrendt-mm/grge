<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Movement extends Model_Log_Message {

    protected static $type = Model_Log_Message::MLM_MOVEMENT_EVENT;

    const MOVEMENT_TYPE_ENTER = 1;
    const MOVEMENT_TYPE_LEAVE = 2;
    const MOVEMENT_TYPE_PASS = 3;

    /**
     * Creates a message that a player has entered or left the place
     * @param number $type
     * @param null|number|String $pid PID or translatable name
     * @internal param \Model_Places_Abstract_Place $ruin Short message title
     */
	public function __construct($type, $pid = null) {
        /** @global Model_Player $player */
        global $player;

        if ($pid === null)
            $pid = $player->user_id();

        if (is_numeric($pid))
            parent::__construct([
                'id' => (int)$pid,
                'class' => $type
            ]);
        else parent::__construct([
            'id' => -1,
            'name' => $pid,
            'class' => $type
        ]);
	}

    protected function postprocess($data) {
        /** @global Model_Player $player */
        /** @global Model_Game $game */
        global $player, $game;

        if (isset($data['name'])) {
            $data['self'] = false;
            $data['name'] = __($data['name']);
        } else {
            $data['self'] = ($data['id'] == $player->id());
            $data['name'] = $game->get_player($data['id'])->name();
        }

        return $data;
    }
}