<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Job_Soldier extends Model_Buffs_Abstract_Job {
	
	protected static $namelist = Array('Reservist', 'Soldat', 'Soldat', 'Soldat', 'Soldat');
	protected static $desclist = Array(	'Du beherrscht die Grundlagen des bewaffneten Kampfes. Waffe ausrichten, Abzug drücken, Zombies beim Umfallen zugucken. Leider ist dein Wissen eher theoretischer Natur, dennoch erhälst du einen Bonus beim Einsatz von Waffen.',
									'All deine Waffen bedienst du routiniert. Während sich andere in den eigenen Fuß schießen, feuerst du einem 200m entfernten Zombie den linken Backenzahn aus dem verfaulenden Mund. Du erhälst einen ordentlichen Bonus beim Einsatz von Waffen.',
									'All deine Waffen bedienst du routiniert. Während sich andere in den eigenen Fuß schießen, feuerst du einem 200m entfernten Zombie den linken Backenzahn aus dem verfaulenden Mund. Du erhälst einen ordentlichen Bonus beim Einsatz von Waffen.',
									'All deine Waffen bedienst du routiniert. Während sich andere in den eigenen Fuß schießen, feuerst du einem 200m entfernten Zombie den linken Backenzahn aus dem verfaulenden Mund. Du erhälst einen ordentlichen Bonus beim Einsatz von Waffen.',
									'All deine Waffen bedienst du routiniert. Während sich andere in den eigenen Fuß schießen, feuerst du einem 200m entfernten Zombie den linken Backenzahn aus dem verfaulenden Mund. Du erhälst einen ordentlichen Bonus beim Einsatz von Waffen.');
	protected static $bid = 'soldier';
	
	protected $effects = Array(
			Model_Status::MS_CHAR_ACCURACY => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			),
	);

    protected function get_effects(): array { return $this->effects; }

	protected function adjust(): void
    {
		$this->effects[Model_Status::MS_CHAR_ACCURACY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = $this->level === 1 ? 0.05 : 0.1;
	}	
}
