<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Drug1 extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Im Drogenrausch';
	protected static $icon = 'drug1';
	protected static $desc = 'Dein Körper ist momentan damit beschäftigt, den Mist wegzuräumen mit dem du ihn gerade geflutet hast. Sei lieber vorsichtig, wenn du jetzt weiter Drogen nimmst wirst du möglicherweise abhängig...';
	protected static $bid = 'drug1';
	
	public function __construct($player_id = NULL, $lifetime) {
		parent::__construct($player_id, $lifetime);
		if ($buff = $this->assoc_player->get_status()->retrieve('drug3')) {
			$buff->unbuff();
			$this->assoc_player->log()->add(new Model_Log_Types_Text('Drogensucht', null, 'Das hat gut getan! Du hast die Entzugserscheinungen gegen rosa Elephanten eingetauscht, die mit geschminkten Aligatoren um zwei Einhörner kämpfen. Zumindest für ein Weilchen...'));
		}
	}
	
	public function merge($newclass) {
		if (!$this->assoc_player->get_status()->retrieve('drug2') && mt_rand(0, 2) < 2) {
			new Model_Buffs_Drug2($this->assoc_player);
			$this->assoc_player->log()->add(new Model_Log_Types_Text('Drogensucht', null, 'Uups, da hast du es wohl ein wenig übertrieben, jetzt bist du drogensüchtig. Hoffentlich hast du entweder ein volles Pillenschränkchen oder zumindest weitreichende Erfahrung mit Entzugserscheinungen...'));
		}
		$this->lifetime += $newclass->lifetime();
		Tool_Numerics::bounds($this->lifetime, 0, 300);
	}
	
	public function unbuff() {
		if ($this->assoc_player->get_status()->retrieve('drug2')) new Model_Buffs_Drug3($this->assoc_player, 864);
		parent::unbuff();
	}
}
