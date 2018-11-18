<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Movement extends Model_Log_Message {

    protected static $type = Model_Log_Message::MLM_MOVEMENT_EVENT;

    public const MOVEMENT_TYPE_ENTER = 1;
    public const MOVEMENT_TYPE_LEAVE = 2;
    public const MOVEMENT_TYPE_PASS = 3;

    /**
     * Creates a message that a player has entered or left the place
     * @param number $type
     * @param null|int|String $pid PID or translatable name
     * @param bool $npc
     * @throws Exception
     */
	public function __construct($type, $pid = null, $npc = false) {
        if ($pid === null && $npc)
            throw new RuntimeException('NPC ID missing!');
        elseif ($pid === null)
            $pid = Globals::PrimaryPlayerF()->id();

        if (is_numeric($pid) || $npc)
            parent::__construct([
                'id' => $npc ? $pid : (int)$pid,
                'class' => $type,
                'npc' => $npc
            ]);
        else parent::__construct([
            'id' => -1,
            'name' => $pid,
            'class' => $type
        ],$pid);
	}

    protected function postprocess($data) {
        if (isset($data['name'])) {
            $data['self'] = false;
            $data['name'] = __($data['name']);
        } else {
            $obj = $data['npc'] ? Globals::CurrentGameF()->get_npc($data['id']) : Globals::CurrentGameF()->get_player($data['id']);
            $data['name'] = $obj ? $obj->name() : "UNKNOWN [{$data['id']}]";
            if ($data['npc']) $data['self'] = false;
        }

        return $data;
    }
}