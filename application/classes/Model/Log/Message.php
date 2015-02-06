<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Message extends Model {

    const MLM_PRERENDERED_STRING = 0;
    const MLM_MOVEMENT_EVENT = 1;
    const MLM_ITEM_LOG = 2;
    const MLM_CHEM_EXPERIMENT = 3;
    const MLM_LOCATION_LOG = 4;

    const MLM_BATTLE_CONTAINER = 5;
    const MLM_BATTLE_ENTER = 6;
    const MLM_BATTLE_ESCAPE = 7;
    const MLM_BATTLE_DEATH = 8;
    const MLM_BATTLE_ATTACK = 9;
    const MLM_BATTLE_INJURY = 10;
    const MLM_BATTLE_ROUND = 11;


    protected $data = [];
    protected $uid;
    private $timestamp;
    protected static $type;

    /**
     *
     * @param mixed $data Any data
     * @param int $uid User ID (optional)
     */
    public function __construct($data, $uid = null) {
        /** @global Model_Game $game */
        /** @global Model_Player $player */
        global $game, $player;
        $this->data = $data;
        $this->uid = $uid !== null ? $uid : ($player ? $player->id() : -1);
        $this->timestamp = $game->now();
    }

    protected function postprocess($data) {
        return $data;
    }

    /**
     * @param bool $plain_data
     * @return array
     */
    public function render($plain_data = false) {
        /** @global Model_Player $player */
        global $player;

        $tmpd = $this->postprocess($this->data);
        if ($plain_data)
            return $tmpd;

        if (is_array($tmpd) && !isset($tmpd['self']))
            $tmpd['self'] = $player->id() === $this->uid;

        return [
            'type' => static::$type,
            'time' => $this->timestamp,
            'data' => $tmpd
        ];
    }

    public function as_notification() {
        return false;
    }

    /**
     * @param Model_Log_Message $merger
     * @return bool
     */
    public function merge($merger) {
        return false;
    }

}
