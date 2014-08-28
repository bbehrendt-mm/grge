<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Treehouse extends Model_Places_Abstract_Hideout {

    protected static $name = 'Baumhaus';
    protected static $description = 'Aufgrund der mangelhaften Kletterfähigkeiten von Zombies gibt dieses Baumhaus ein überraschend gutes Versteck ab. Im Brandfall solltest du es jedoch lieber nicht verwenden...';
    protected static $icon = 'treehouse';

    //Base defense
    protected $defense = 10;

    //Base: 15% per day
    protected static $decay_rate = 0.15;

    //Exp: 8% per day
    protected static $decay_exp = 0.15;

    public function uin($new = null) {
        if ($new !== null) {
            $this->home_extensions("bed", "lv1", true);
        }
        return parent::uin($new);
    }

    public function can_enter($pid = null) {
        global $game;

        if ($pid === null)
            global $player;
        else $player = $game->get_player($pid);

        if (!$player->job(1080))
            $player->log()->add('An der Tür dieses Baumhauses befindet sich ein Schild, auf dem in krakeliger Schrift geschrieben steht: "Führ Erwaksene ferboten!!!". So ein Ärger aber auch...');

        return $player->job(1080);
    }

    public function mapable() {
        return false;
    }

}	