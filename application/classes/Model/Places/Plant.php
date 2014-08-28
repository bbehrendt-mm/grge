<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Plant extends Model_Places_Abstract_Place {
	
	protected static $name = 'Kernkraftwerksruine';
	protected static $description = 'Zäune und Warnschilder umranden die gigantische Stahlbetonkuppel, in der du dich nun befindest. Dieser Kernreaktor hat vor langer Zeit viele Städte mit Energie versorgt, jetzt tut er nicht mehr sonderlich viel (außer die Umgebung zu verstrahlen). Wenn du keine Angst vor Strahlung hast, kannst du hier nach Überresten des Kühlwassers oder nach Ersatzteilen suchen.';
    protected static $icon = 'plant';
    protected static $outside = false;
	
	public function tick() {
		global $player;
		$player->stats_modify(Model_Player::MP_STAT_RADIATION, 3.5);
		parent::tick();
	}

    public function uin($uin = NULL) {
        if ($uin !== null)
            $this->inventory->add(new Model_Items_Virtual_Location_Plant());

        return parent::uin($uin);
    }
}	