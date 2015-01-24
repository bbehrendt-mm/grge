<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Message extends Model {

    const MLM_PRERENDERED_STRING = 0;
    const MLM_MOVEMENT_EVENT = 1;

    protected $data = [];
    private $timestamp;
    protected static $type;

    /**
     *
     * @param mixed $data Any data
     */
    public function __construct($data) {
        /** @global Model_Game $game */
        global $game;
        $this->data = $data;
        $this->timestamp = $game->now();
    }

    protected function postprocess($data) {
        return $data;
    }

    /**
     * @return array
     */
    public function render() {
        return [
            'type' => static::$type,
            'time' => $this->timestamp,
            'data' => $this->postprocess($this->data)
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
