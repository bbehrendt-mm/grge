<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Backpack extends Model_Buffs_Abstract_Passive {
	
	protected static $name = 'Schwer beladen';
	protected static $icon = 'backpack';
	protected static $desc = 'Dein Rucksack ist bis zum Anschlag gefüllt. Dieses Ding durch die Gegend zu schleppen wird sicher eine Menge Kraft kosten... bist du sicher, dass du nicht ein paar Dinge hier lassen kannst?';
	protected static $bid = 'backpack';

    protected function get_effects(): array { return [
        Model_Status::MS_CHAR_DISTANCING => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0.4,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0
        )];
    }
	
	protected function activator(): bool
    {
        return ($this->assoc_player->inventory()->global_limit() > 0) ? (($this->assoc_player->inventory()->weight() / $this->assoc_player->inventory()->global_limit()) >= 0.9) : false;
	}
}
