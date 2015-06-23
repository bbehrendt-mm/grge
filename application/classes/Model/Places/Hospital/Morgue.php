<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Hospital_Morgue extends Model_Places_Abstract_Place {
	
	protected static $name = 'Leichenhalle';
	protected static $description = 'Es scheint, als hätte der diensthabende Pathologe versucht, sich hier drin zu verbarrikadieren. Die schwere Metalltür ist fest verschlossen, und der Öffnungsmechanismus ist zerstört. Leider hat er nicht bedacht, dass er sich in einer LEICHENHALLE befindet - mit Leichen, die vielleicht noch nicht vollständig verstorben sind. Wenigstens scheint er vor seinem Tod ordentlich Schaden angerichtet zu haben...';
    protected static $icon = 'hospital_morgue';
    protected static $outside = false;

    public function uin($uin = NULL) {
        $t = parent::uin($uin);
        if ($uin !== null) {
            $this->inventory->add(new Model_Items_Body('Pathologe Quincy'));
            $num = mt_rand(0,4);
            for ($i = 0; $i < $num; $i++)
                $this->inventory->add(new Model_Items_Body2());
        }
        return $t;
    }

    //Enter location
    public function can_enter($pid = null) {
        /** @global Model_Game $game */
        global $game;

        if ($pid === null)
            global $player;
        else $player = $game->get_player($pid);

        if (!$player->job(1080))
            $player->log()->add('Die Tür zur Leichenhalle ist fest versiegelt und lässt sich nicht öffnen. Über dir befindet sich ein kleines Lüftungsgitter, über das du vermutlich in die Leichenhalle gelangen könntest - wenn du hinein passen würdest. Echt Mist dass du kein Kind mehr bist...');

        return $player->job(1080);
    }
}	