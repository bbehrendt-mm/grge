<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Traits_Loner extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Aussenseiter';
	protected static $icon = 'traits/loner';
	protected static $desc = 'Du bist der Typ, der irgendwie immer da ist, aber den niemand so richtig wahrnimmt. Das hat sich auch in der Zombieapokalypse nicht geändert. Deine Chance, Zombies bei einem Kampf zu entkommen, steigt um 15%. Außerdem wird anderen Spielern im Mehrspielermodus deine Präsenz weniger deutlich angezeigt.';
	protected static $bid = 'tr_loner';

    protected $effects = Array(
        Model_Player::MP_CHAR_EVASIVENESS => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0.15,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
    );
}
