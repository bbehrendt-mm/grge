<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Built extends Model implements Interface_Message {

    const MLTB_BUILD = 1;
    const MLTB_WORKBENCH = 2;
    const MLTB_KITCHEN = 3;
    const MLTB_GENERATOR = 4;
    const MLTB_DEFENSE = 5;
    const MLTB_VARIOUS = 6;

    private $type;
	private $project;
    private $effects;
    private $player;
	private $timecode;

    /**
     * Creates a build message
     * @param $type
     * @param $project
     * @param $pid
     * @param null $effects
     */
	public function __construct($type, $project, $pid, $effects = null) {
		$this->type = $type;
        $this->project = $project;
        $this->player = $pid;
        $this->effects = $effects;
		
		$this->timecode = time();
	}
	
	public function render_title() {
        return null;
	}
	
	public function render_body() {
        /**
         * @global Model_Player $player
         * @global Model_Game $game
         */
        global $player, $game;
        $r = "";

        if ($player->id() == $this->player)
            switch ($this->type) {
                case static::MLTB_BUILD:
                    $r = 'Du hast dieses Versteck durch ein/eine/einen :project aufgewertet!';
                    break;
                case static::MLTB_WORKBENCH:
                    $r = 'Du hast an der Werkbank :project hergestellt.';
                    break;
                case static::MLTB_DEFENSE:
                    $r = 'Du hast mithilfe der/des :project :zombies Zombies vor dem Versteck vernichtet.';
                    break;
                case static::MLTB_GENERATOR:
                    $r = 'Du hast den Generator in Gang gesetzt :project für das Versteck produziert.';
                    break;
                case static::MLTB_KITCHEN:
                    $r = 'Du hast in der Kücke ein/e leckere/s :project gekocht. Mjam!';
                    break;
                case static::MLTB_VARIOUS:
                    $r = 'Du hast :project mithilfe der Gerätschaften an diesem Ort hergestellt.';
                    break;
            }
        else switch ($this->type) {
            case static::MLTB_BUILD:
                $r = ':name hat dieses Versteck durch ein/eine/einen :project aufgewertet!';
                break;
            case static::MLTB_WORKBENCH:
                $r = ':name hat an der Werkbank :project hergestellt.';
                break;
            case static::MLTB_DEFENSE:
                $r = ':name hat mithilfe der/des :project :attv Zombies vor dem Versteck vernichtet.';
                break;
            case static::MLTB_GENERATOR:
                $r = ':name hat den Generator in Gang gesetzt und :project für das Versteck produziert.';
                break;
            case static::MLTB_KITCHEN:
                $r = ':name hat in der Kücke ein/e leckere/s :project gekocht. Mjam!';
                break;
            case static::MLTB_VARIOUS:
                $r = ':name hat :project mithilfe der Gerätschaften an diesem Ort hergestellt.';
                break;
        }

        return __($r, array(':name' => $game->get_player($this->player)->name(), ':project' => __($this->project), ':attv' => $this->effects));
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