<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Strangewood_Path extends Model_Places_Abstract_Trap implements Interface_Corridor {

    protected static $chance = 0.1;
	
	protected static $location_name = 'Trampelpfad';
	protected static $description = 'Dieser Pfad wird von unfassbar dichtem Gebüsch begrenzt - es scheint absolut unmöglich, von ihm abzuweichen.';
    protected static $outside = true;

    protected $player_ids = [];

    public function battle_location_type(): string
    {
        return $this->is_outside() ? 'hw19_outside' : 'hw19_inside';
    }

    public function enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool {
        $b = parent::enter($pid, $type);
        if ($b && Tool_Scripts::is_npc(Globals::CurrentGameF()->get_player($pid) ) && !isset($this->player_ids[$pid] )) {
            $this->player_ids[$pid] = true;
            Globals::CurrentGameF()->get_player($pid)->achievements()->achieve(Model_Achievement::MA_HALLOWEEN_19_1);
        }
        return $b;
    }
}