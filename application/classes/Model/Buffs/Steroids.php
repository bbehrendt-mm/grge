<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Steroids extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Steroide';
	protected static $icon = 'steroids';
	protected static $desc = 'Du stehst unter dem Einfluss von Steroiden. Du kannst nun wesentlich mehr Gegenstände tragen.';
	protected static $bid = 'steroids';

    public function merge(Model_Buffs_Abstract_Buff $newclass): void
    {
        $this->lifetime += $newclass->lifetime();
        Tool_Numerics::bounds($this->lifetime, 0, 288);
    }

    protected function apply(): void
    {
        parent::apply();
        $this->assoc_player->inventory()->limit($this->assoc_player->inventory()->limit() + 50);
    }

    public function unbuff(): bool {
        $this->assoc_player->inventory()->limit($this->assoc_player->inventory()->limit() - 50);
        return parent::unbuff();
    }
}
