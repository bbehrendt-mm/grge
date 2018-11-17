<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Treehouse extends Model_Places_Abstract_Hideout {

    protected static $location_name = 'Baumhaus';
    protected static $description = 'Aufgrund der mangelhaften Kletterfähigkeiten von Zombies gibt dieses Baumhaus ein überraschend gutes Versteck ab. Im Brandfall solltest du es jedoch lieber nicht verwenden...';
    protected static $icon = 'treehouse';
    protected static $upgradable = false;

    //Base deco value
    protected static $base_deco_value = 15;

    //Base defense
    protected static $base_defense = 10;

    //Base: 15% per day
    //protected static $decay_rate = 0.15;

    //Exp: 8% per day
    protected static $decay_exp = 0.15;

    public function uin($new = null) {
        if ($new !== null)
            $this->setup_new_room($this->room(),
                                  ['bedroom'],
                                  ['bedr1']);

        return parent::uin($new);
    }

    public function can_enter_map($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER) {
        if (!$pid) $player = Globals::CurrentPlayerF();
        elseif ($type == Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGameF()->get_player($pid);
        else $player = Globals::CurrentGameF()->get_npc($pid);

        if ($type == Interface_Tickable::IT_TYPE_PLAYER && !$player->job(1080))
            $player->log()->add('An der Tür dieses Baumhauses befindet sich ein Schild, auf dem in krakeliger Schrift geschrieben steht: "Führ Erwaksene ferboten!!!". So ein Ärger aber auch...');

        return $type == Interface_Tickable::IT_TYPE_PLAYER && $player->job(1080);
    }

    public function mapable() {
        return false;
    }

}	