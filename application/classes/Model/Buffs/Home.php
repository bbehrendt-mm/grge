<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Home extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Insel der Ruhe';
	protected static $icon = 'home';
	protected static $desc = 'Endlich mal ein bisschen ausruhen. Zuhause ist dein Wasser- und Nahrungsverbrauch leicht reduziert. Wenn du dein Versteck hübsch gestaltest, erhälst du sogar einen Bonus auf die Regeneration von Energie und Gesundheit.';
	protected static $bid = 'home';

    protected $effects = Array(
        Model_Status::MS_STAT_HUNGER => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => -0.2,
        ),
        Model_Status::MS_STAT_THIRST => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => -0.3,
        ),
        Model_Status::MS_STAT_HEALTH => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
        Model_Status::MS_STAT_ENERGY => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
    );

    protected function get_effects(): array { return $this->effects; }

    public function rebuild() {
        if (!($hideout = Tool_Scripts::current_location_hideout($this->assoc_player)))
            return parent::rebuild();
        $deco = ($this->associated_to_player()) ? $hideout->deco() : 0;

        $this->effects[Model_Status::MS_STAT_HEALTH] = Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => min(50,max(0,$deco-100)/4)/100,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        );

        $this->effects[Model_Status::MS_STAT_ENERGY] = Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => $deco < 0 ? min(0,max(-100,$deco))/500 : min(250,max(0,$deco))/500,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        );

        return parent::rebuild();
    }
}
