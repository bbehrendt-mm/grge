<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Job_Pathfinder extends Model_Buffs_Abstract_Job {
	
	protected static $name = Array('Pfadfinder-Frischling', 'Pfadfinder', 'Erfahrender Pfadfinder', 'Pfadfinder-Experte', 'Pfadfindermeister');
	protected static $desc = Array(	'In deiner kurzen Zeit bei den Pfadfindern hast du zumindest deine Gehtechnik perfektionieren können. Du benötigst nun geringfügig weniger Energie, um an neue Orte zu gelangen.',
									'Du weist, dass der offensichtliche Weg nicht immer der effektivste ist. Deine Fähigkeit Wege zu optimieren hilft dir, Energie zu sparen.',
									'Weder Stock noch Stein können dich aufhalten. Wo andere Umwege machen, läufst du einfach mitten durch. Deine Kenntnis der Umgebung reduziert deine Bewegungskosten merklich.',
									'Überall siehst du Schlupflöcher, kleine Felsspalten und überwucherte Trampelpfade. Kein noch so versteckter Weg bleibt dir verborgen. Du kannst nicht nur auf bekannten Wegen gewaltig Energie sparen, sondern auch versuchen, komplett neue Pfade zu entdecken.',
									'Für dich gibt es 1001 Wege, um zum Ziel zu kommen. Und einen davon haben die Zombies sicher noch nicht entdeckt...  Du hast eine erhöhte Chance, aus Kämpfen zu fliehen oder blockierenden Zombies kampflos zu entkommen.');
	protected static $bid = 'pathfinder';
	
	protected $effects = Array(
			Model_Status::MS_CHAR_DISTANCING => Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			),
			Model_Status::MS_CHAR_EVASIVENESS => Array(
				Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
				Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			),
            Model_Status::MS_CHAR_LOCATION_SPAWNRATE => Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            ),
	);
	
	protected function adjust() {
		$this->effects[Model_Status::MS_CHAR_DISTANCING][Model_Buffs_Abstract_Buff::MB_DROP_ACC] = min(4, $this->level) * 0.05;
		if ($this->level >= 4) $this->effects[Model_Status::MS_CHAR_EVASIVENESS][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.1;
        if ($this->level >= 5) $this->effects[Model_Status::MS_CHAR_LOCATION_SPAWNRATE][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.15;
	}	
}
