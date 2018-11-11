<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Message extends Model {

    const MLM_PRERENDERED_STRING = 0;
    const MLM_MOVEMENT_EVENT = 1;
    const MLM_ITEM_LOG = 2;
    const MLM_CHEM_EXPERIMENT = 3;
    const MLM_LOCATION_LOG = 4;
    const MLM_COMBAT = 5;
    const MLM_RAW_DATA = 6;
    const MLM_TRANSACTION_LOG = 7;



    protected $data = [];
    protected $uid;
    private $timestamp;
    private $ticks = -1;
    protected static $type;

    /**
     *
     * @param mixed $data Any data
     * @param int   $uid  User ID (optional)
     *
     * @throws Exception
     */
    public function __construct($data, $uid = null) {
        $this->data = $data;
        $this->uid = $uid !== null ? $uid : (Globals::hasPrimaryPlayer() ? Globals::PrimaryPlayerF()->id() : (Globals::hasCurrentUser() ? Globals::CurrentUserF()->uid() : -1));
        $this->timestamp = Globals::CurrentGameF() ? Globals::CurrentGameF()->now() : time();
        $this->ticks = Globals::CurrentGameF() ? Globals::CurrentGameF()->duration() : -1;
    }

    protected function postprocess($data) {
        return $data;
    }

    /**
     * @param bool $plain_data
     * @return array
     * @throws Kohana_Exception
     */
    public function render($plain_data = false) {
        $tmpd = $this->postprocess($this->data);
        if ($plain_data)
            return $tmpd;

        if (is_array($tmpd) && !isset($tmpd['self']))
            $tmpd['self'] = (Globals::PrimaryPlayerF()->id() === $this->uid || Globals::CurrentUserF()->uid() === $this->uid);

        return [
            'type' => static::$type,
            'time' => $this->timestamp,
            'gt' => $this->ticks >= 0 ? Tool_Scripts::get_daytime($this->ticks)->getTimestamp() : null,
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
