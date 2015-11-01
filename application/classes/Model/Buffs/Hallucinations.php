<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Hallucinations extends Model_Buffs_Abstract_Buff {

    protected static $name = 'Schwere Halluzinationen';
    protected static $icon = 'hallucination';
    protected static $desc = 'Keine Angst, alles ist in Ordnung; das behauptet zumindest der grüne Elefant, der auf deiner Schulter sitzt. Moment, steht da drüben etwa Helmut Berger neben der fliegenden Schokoladenpalme, an der hölzerne Bullenhaie wachens?';
    protected static $bid = 'hallucination';

    public function tick() {
        if (!$this->assoc_player->buff_retr('passout') && mt_rand(0,5) > 4) {
            $z = array();
            $c = mt_rand(1,8);
            for ($i = 0; $i < $c; $i++)
                $z[] = new Model_Combat_Zombies_Hallucination();

            Tool_Scripts::combat([[$this->assoc_player], $z], false, 20, $this->assoc_player->location(), 'Ein Kampf ... ?', 'OH GOTT! Du wirst von obskuren Gestalten angegriffen, die eventuell mit deinen schweren Halluzinationen in Zusammenhang stehen!');
        }

        return parent::tick();
    }
}
