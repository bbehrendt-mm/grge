<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Read extends Model_Buffs_Abstract_Fragile {
	
	protected static $name = 'Lesen';
	protected static $desc = 'Du hast es dir gemütlich gemacht und liest ein gutes Buch. Daher kannst du im Moment keine Aktion durchführen und diesen Ort nicht verlassen.';
	protected static $icon = 'read';

	protected static $abortable = true;
    protected static $allow_npc_assoc = false;

    private $item_id;

    public function __construct($itemid, $effects, $lifetime, $player_id = null) {
        parent::__construct($player_id, $lifetime);

        $this->item_id = $itemid;

        if ($this->assoc_player->location()->has_upgrade("sofa2")) {
            $f = 1.15;
            if (!isset($effects[Model_Status::MS_STAT_ENERGY])) $effects[Model_Status::MS_STAT_ENERGY] = 0;
            if (!isset($effects[Model_Status::MS_STAT_SLEEPY])) $effects[Model_Status::MS_STAT_SLEEPY] = 0;
        } else $f = 1;

        foreach ($effects as $stat => $value)
                $this->effects[$stat] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => ($value > 0) ? $value * $f : 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => ($value < 0) ? -$value * $f : 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            );

        if ($this->assoc_player->location()->has_upgrade("sofa2")) {
            $this->effects[Model_Status::MS_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] += 0.20;
            $this->effects[Model_Status::MS_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_DROP_ACC] += 0.20;
        }
    }

	protected function action_on_complete() {
        $this->assoc_player->log()->add('Und wieder hast du ein Buch durchgelesen. Nur das Ende hätte etwas spannender sein können...');
        foreach ($this->effects as $stat => $data) {
            if ($data[Model_Buffs_Abstract_Buff::MB_RAISE_ACC] != 0)
                $this->assoc_player->get_status()->modify($stat, $data[Model_Buffs_Abstract_Buff::MB_RAISE_ACC], Model_Status::MS_EFFECT_BUFF);
            if ($data[Model_Buffs_Abstract_Buff::MB_DROP_ACC] != 0)
                $this->assoc_player->get_status()->modify($stat, -$data[Model_Buffs_Abstract_Buff::MB_DROP_ACC], Model_Status::MS_EFFECT_BUFF);
        }

        return;
	}

    public function tick() {
        /** @global Model_Game $game */
        global $game;
        if (!$game->item_available($this->item_id) || !($item = $game->uin()->get($this->item_id, 'Model_Items_Abstract_Book'))) {
            $this->cancel();
            return;
        }

        parent::tick();

        /** @var Model_Items_Abstract_Book $item */
        if (!$item->read($this->assoc_player->id()))
            $this->unbuff();
    }
	
	public function cancel() {
		if ($buff = $this->assoc_player->get_status()->retrieve('sleep_cozy'))
			$buff->unbuff();
		$this->assoc_player->log()->add('Zeit, die Lektüre wegzulegen und wieder in die reale Welt einzusteigen, die in Wahrheit gar nicht real sondern ein Browserspiel ist.');
		parent::cancel();
	}
}