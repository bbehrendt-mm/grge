<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Magicbox extends Model_Items_Abstract_Item {
	
	protected static $static_info = Array(
			'name' => 'Magische Box',
			'icon' => 'magicbox',
			'description' => 'Dieses Item wurde zu Testzwecken implementiert und erlaubt es, beliebige andere Items zu erzeugen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);
	
	protected static $weight = 0;
	protected static $essential = true;	
	protected static $associated_view = 'magicbox';

    private function materialize_items($list) {
        global $game, $player;

        $list = str_replace(array('#.', '#'), array('Model_Items_Generic_', 'Model_Items_'), $list);

        $items = explode(' ', $list);
        $ret = array();
        foreach ($items as $item) if (strpos($item, 'Model_Items_') === 0 && strpos($item, 'Model_Items_Abstract_') === false)
        {
            $lo = explode(':', $item, 2);
            if (!isset($lo[1]))
                $lo[1] = 1;

            for ($i = 0; $i < $lo[1]; $i++)
                $ret[] = new $lo[0];
        }

        return $ret;
    }

	public function interaction_spawn_rucksack($item_l) {
		global $player;

        foreach ($this->materialize_items($item_l) as $item)
            $player->inventory()->add($item);

		return true;
	}
	
	public function drop($silent = false) {
		/** @global Model_Player $player */
		global $player;

        if (!$silent) $player->log()->add(new Model_Log_Types_Text(null, null, 'Dieses Item kann nicht abgelegt werden!', true));
		return false;
	}
	
	public function interaction_spawn_location($item_l) {
		global $player;

        foreach ($this->materialize_items($item_l) as $item)
            $player->location()->inventory()->add($item);

		return true;
	}

	public function interaction_spawn_building($location) {
		global $game, $player;	
		
		if (strpos($location, 'Model_Places_') === 0 && strpos($location, 'Model_Places_Abstract_') === false)
		{
			$b = new $location;
			$player->location()->add_map_node($b, $b->name() . ' (magic)');
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Erfolgreich.', true));
		}
		else $player->log()->add(new Model_Log_Types_Text(null, null, 'Gebäude-Bezeichner ungültig!', true));		
		return true;
	}
	
	public function interaction_statdebug() {
		global $game, $player;
		
		$ret = Array();
		foreach ($player->active_stats() as $id)
			$ret[] = $id . " => " . $player->stats_get($id) . " (" . $player->stats_buffs($id) . ")";
		
		$player->log()->add(new Model_Log_Types_Text(null, null, 'Status Dump<br />' . implode('<br />', $ret), true));
	}
	
	public function interaction_achdebug() {
		global $game, $player;
	
		$ret = Array();
		
		foreach ($player->achievements()->get_all() as $aid => $count)
			$ret[] = $aid . " => <img src=\"/application/assets/icons/achievements/{$aid}.gif\" /> (" . $count . ")";
	
		$player->log()->add(new Model_Log_Types_Text(null, null, 'Achievement Dump<br />' . implode('<br />', $ret), true));
	}
	
	public function interaction_battle($data) {
		global $game, $player;	
		$data = explode('|', $data);
		
		if (!(count($data)&1)) $player->log()->add(new Model_Log_Types_Text(null, null, 'Argument ungültig!', true));
		if (count($data) == 1) $data = Array(1, 'Model_Battle_Shambler', $data[0]);

        $z = array();
		for ($i = 1; $i < count($data); $i+=2) {
			$conf = explode('x', $data[$i+1]);
			if (count($conf) != 2 || $conf[0] <= 0 || $conf[1] < 0 || $conf[1] > 100 || strpos($data[$i], 'Model_Battle_') !== 0)
			{
				$player->log()->add(new Model_Log_Types_Text(null, null, 'Argument ungültig!'));
				return false;
			}
            $z[] = new $data[$i]($conf[0], $conf[1]);
		}

        $battle_log = Tool_Scripts::battle($z, Tool_Scripts::at_location(), (int)$data[0], $battle, $zc);
		$player->location()->log()->add(new Model_Log_Types_Battle('Debug Battle (Magic)', $battle_log));
		
		return true;
	}
	
	public function interaction_accum($data) {
		global $game, $player;
		$data = (int)$data;
	
		$player->location()->zombie_factory()->accumulate_zombies($data);
	
		return true;
	}
	
	public function interaction_achieve($data) {
		global $game, $player;	
		$data = explode('x', $data);
		if (count($data) != 2 || $data[0] <= 0)
		{
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Argument ungültig!', true));
			return false;
		}
		
		$player->achievements()->achieve($data[0], $data[1]);
		return true;
	}
	
	public function interaction_tick($data) {
		global $game, $player;	
		$data = round($data);
		
		if ($data < 1)
		{
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Argument ungültig!', true));
			return false;
		}
		
		$game->tick_length($data);
		return true;
	}
	
	public function interaction_find_item() {
		global $game, $player;	
		
		$player->location()->find_item(true);
		return true;
	}

    public function interaction_uncover_map() {
        global $game, $player;
        $game->map($player->location_class())->uncover_all();
        return true;
    }

    public function interaction_regenerate() {
        global $game, $player;

        $player->stats_set(Model_Player::MP_STAT_ENERGY, 100, Model_Player::MP_STAT_HEALTH, 100, Model_Player::MP_STAT_HUNGER, 100, Model_Player::MP_STAT_THIRST, 100, Model_Player::MP_STAT_SLEEPY, 100);
        return true;
    }

	public function interaction_suicide() {
		global $game, $player;
		$game->stats(Model_Game::MGLS_Health, -1000);
		$player->log()->add(new Model_Log_Types_Text(null, null, 'Erfolgreich.', true));
	}
	
	public function drop_dead() {
		return null;
	}
}	