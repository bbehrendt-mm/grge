<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Tentkit2 extends Model_Places_Tentkit {

    protected static $name = 'InstaZELT™ Deluxe';
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
        if ($new !== null)
            Model_Blueprints::fast_apply($this, 'upgrades', 'bedr2');

        return parent::uin($new);
    }

    public function can_enter($pid = null) {
        /** @global Model_Game $game */
        global $game;

        if ($pid === null)
            global $player;
        else $player = $game->get_player($pid);

        if (count(Tool_Scripts::at_location($this->uin())) >= 3) {
            $player->log()->add('Das InstaZELT™ Deluxe ist zwar vergleichsweise groß, aber trotzdem passen nur drei Personen gleichzeitig hinein...');
            return false;
        } else return true;
    }
}	