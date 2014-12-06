<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Movement extends Model implements Interface_Message {

    private $uin;
    private $name = null;
	private $type;
	private $timecode;

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
        global $player;
        if ($pid === null)
            $this->uin = $player->user_id();
        elseif (is_numeric($pid))
            $this->uin = $pid;
        else {
            $this->uin = -1;
            $this->name = $pid;
        }

		$this->type = $type;
		$this->timecode = time();
	}
	
	public function render_title() {
		return null;
	}
	
	public function render_body() {
        global $player, $game;

        $s = null;
        switch ($this->type) {
            case static::MOVEMENT_TYPE_ENTER:
               $s = ($player->user_id() == $this->uin) ? 'Du hast diesen Ort betreten.' : ':name hat diesen Ort betreten.';
                break;
            case static::MOVEMENT_TYPE_LEAVE:
                $s = ($player->user_id() == $this->uin) ? 'Du hast diesen Ort verlassen.' : ':name hat diesen Ort verlassen.';
                break;
            case static::MOVEMENT_TYPE_PASS:
                $s = ($player->user_id() == $this->uin) ? 'Du hast diesen Ort auf deinem Weg passiert.' : ':name hat diesen Ort auf seinem Weg passiert.';
                break;
        }

        return $s ? __($s, array(':name' => ($this->uin <= 0) ? __($this->name) :  $game->get_player($this->uin)->name())) : '[ERROR] MOVEMENT: META_UNPACK_CRITICAL_FAILURE';
	}
	
	public function timecode() {
		return $this->timecode;
	}

    /**
     * @param Interface_Message $new
     * @return bool
     */
    public function merge($new) {
        return false;
    }
}