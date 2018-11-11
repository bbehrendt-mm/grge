<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Tentkit2 extends Model_Places_Tentkit {

    protected static $location_name = 'InstaZELT™ Deluxe';
    protected static $icon = 'itent2';

    //Base deco value
    protected static $base_deco_value = 15;

    //Base defense
    protected $defense = 8;

    //Base: 15% per day
    protected static $decay_rate = 0.15;

    //Exp: 8% per day
    protected static $decay_exp = 0;

    public function uin($new = null) {
        $t = parent::uin($new);

        if ($new !== null)
            $this->setup_new_room($this->room(),
                                  [],
                                  ['bedr2']);
        return $t;
    }

    public function can_enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER) {
        $is_player = true;
        if (!$pid) $player = Globals::CurrentPlayerF();
        elseif ($type == Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGameF()->get_player($pid);
        else {
            $is_player = false;
            $player = Globals::CurrentGameF()->get_npc($pid);
        }

        if (count(Tool_Scripts::at_location($this->uin(), $is_player, !$is_player)) >= 3) {
            if ($type == Interface_Tickable::IT_TYPE_PLAYER)
                $player->log()->add('Das InstaZELT™ Deluxe ist zwar vergleichsweise groß, aber trotzdem passen nur drei Personen gleichzeitig hinein...');
            return false;
        } else return true;
    }
}	