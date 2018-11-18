<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Job_Woman extends Model_Buffs_Abstract_Job {
	
	protected static $name = Array('Matriarchiale Kampfkunst', 'Matriarchiale Kampfkunst', 'Matriarchiale Kampfkunst', 'Matriarchiale Kampfkunst', 'Matriarchiale Kampfkunst', 'Matriarchiale Kampfkunst', 'Matriarchiale Kampfkunst', 'Matriarchiale Kampfkunst', 'Matriarchiale Kampfkunst', 'Matriarchiale Kampfkunst');
	protected static $desc = Array(	'Mit einer richtig angepissten Emanzipationsterroristin legen sich selbst Zombies nur ungerne an. Du richtest im Kampf wesentlich mehr Schaden an, steckst dafür aber auch etwas mehr Schaden ein.',
									'Mit einer richtig angepissten Emanzipationsterroristin legen sich selbst Zombies nur ungerne an. Du richtest im Kampf wesentlich mehr Schaden an, steckst dafür aber auch etwas mehr Schaden ein.',
									'Mit einer richtig angepissten Emanzipationsterroristin legen sich selbst Zombies nur ungerne an. Du richtest im Kampf wesentlich mehr Schaden an, steckst dafür aber auch etwas mehr Schaden ein.',
									'Mit einer richtig angepissten Emanzipationsterroristin legen sich selbst Zombies nur ungerne an. Du richtest im Kampf wesentlich mehr Schaden an, steckst dafür aber auch etwas mehr Schaden ein.',
									'Mit einer richtig angepissten Emanzipationsterroristin legen sich selbst Zombies nur ungerne an. Du richtest im Kampf wesentlich mehr Schaden an, steckst dafür aber auch etwas mehr Schaden ein.',
									'Mit einer richtig angepissten Emanzipationsterroristin legen sich selbst Zombies nur ungerne an. Du richtest im Kampf wesentlich mehr Schaden an, steckst dafür aber auch etwas mehr Schaden ein.',
									'Mit einer richtig angepissten Emanzipationsterroristin legen sich selbst Zombies nur ungerne an. Du richtest im Kampf wesentlich mehr Schaden an, steckst dafür aber auch etwas mehr Schaden ein.',
									'Mit einer richtig angepissten Emanzipationsterroristin legen sich selbst Zombies nur ungerne an. Du richtest im Kampf wesentlich mehr Schaden an, steckst dafür aber auch etwas mehr Schaden ein.',
									'Mit einer richtig angepissten Emanzipationsterroristin legen sich selbst Zombies nur ungerne an. Du richtest im Kampf wesentlich mehr Schaden an, steckst dafür aber auch etwas mehr Schaden ein.',
									'Mit einer richtig angepissten Emanzipationsterroristin legen sich selbst Zombies nur ungerne an. Du richtest im Kampf wesentlich mehr Schaden an, steckst dafür aber auch etwas mehr Schaden ein.');
	
	protected static $bid = 'woman';
	
	protected $effects = Array(
        Model_Status::MS_CHAR_DAMAGE_RESISTANCE => Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
        Model_Status::MS_CHAR_DAMAGE_MULTIPLIER => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),

	);
	
	protected function adjust(): void
    {
		$this->effects[Model_Status::MS_CHAR_DAMAGE_RESISTANCE][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = $this->level * 0.020;
        $this->effects[Model_Status::MS_CHAR_DAMAGE_MULTIPLIER][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = $this->level * 0.035;
	}	
}
