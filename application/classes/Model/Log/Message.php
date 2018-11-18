<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Message extends Model {

    public const MLM_PRERENDERED_STRING = 0;
    public const MLM_MOVEMENT_EVENT = 1;
    public const MLM_ITEM_LOG = 2;
    public const MLM_CHEM_EXPERIMENT = 3;
    public const MLM_LOCATION_LOG = 4;
    public const MLM_COMBAT = 5;
    public const MLM_RAW_DATA = 6;
    public const MLM_TRANSACTION_LOG = 7;



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
        $this->uid = $uid ?? Globals::hasPrimaryPlayer()
                ? Globals::PrimaryPlayerF()->id()
                : (Globals::hasCurrentUser() ? Globals::CurrentUserF()->uid()
                    : -1);
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
    public function render($plain_data = false): array
    {
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

    public function as_notification(): bool
    {
        return false;
    }

    /**
     * @param Model_Log_Message $merger
     *
     * @return bool
     */
    public function merge($merger): bool {
        return false;
    }

}
