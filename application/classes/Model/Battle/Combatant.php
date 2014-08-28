<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Battle_Combatant {
	
	protected static $max_health = 100;
	protected $health = 100;
	
	protected static $max_energy = 100;
	protected $energy = 100;
	
	protected $evasiveness = 1;
	protected $accuracy = 1;
	
	protected static $zombie_fraction = false;
	protected $distance = 10;
	
	protected $lock = 0;
	protected static $speed = 1;
	
	protected static $unarmed = 'Model_Battle_Unarmed_Fist';
	protected $resistance_mod;
    protected $damage_mod;

    protected $special = false;

	/**
	 * 
	 * @var Model_Inventory
	 */
	protected $inventory;
	protected $name;
	protected $count;
	
	public function __construct(&$inventory, $distance, $name, $count, $res_mod = 1, $dmg_mod = 1) {
		$this->inventory = $inventory;
		$this->distance = $distance;
		$this->name = $name;
		$this->count = $count;
		$this->resistance_mod = $res_mod;
        $this->damage_mod = $dmg_mod;
	}
	
	/**
	 * Saves a log entry for current round
	 * @param Interface_Message $entry
	 */
	protected function log_entry($entry) {
		global $battle_log, $battle_round;
		
		if (!isset($battle_log[$battle_round])) $battle_log[$battle_round] = Array();
		$battle_log[$battle_round][] = $entry;
	}

    public function special() {
        return $this->special;
    }
	
	public function escape() {
		return true;
	}
	
	public function distance() {
		if (static::$zombie_fraction) return $this->distance;
		else return 0;
	}
	
	public function evasiveness() {
		return $this->evasiveness;
	}
	
	public function count() {
		return $this->count;
	}
	
	public function health() {
		return $this->health;
	}
	
	public function name() {
		return $this->name;
	}
	
	public function is_zombie() {
		return static::$zombie_fraction;
	}
	
	public function stack_health() {
		return $this->health + (($this->count - 1) * static::$max_health);
	}
	
	public function damage($dmg) {
		if ($this->count <= 0) return 0;
		
		$dmg *= $this->resistance_mod;
		
		$kills = 0;
		$ctmp = $this->count;
		if ($dmg < $this->health) $this->health -= $dmg;
		else {
			$dmg -= $this->health;
			
			$this->count = max(0,($this->count - 1) - floor($dmg / static::$max_health));
			Tool_Numerics::bounds($this->count, 0, $this->count);
			$kills = $ctmp - $this->count;
			
			$this->health = static::$max_health - ($dmg - (floor($dmg / static::$max_health) * $dmg));
			Tool_Numerics::bounds($this->health, 0, static::$max_health);
		}
		
		if ($this->count <= 0)
			$this->health = 0;
		
		return $kills;
	}
	
	/**
	 * 
	 * @param Model_Battle_Weapon $item
	 * @param Model_Battle_Combatant $combatant
     * @return float|int
     */
	private function potential($item, $combatant) {
		$rng = $item::$range;

        if ($combatant->count() <= 0)
            return 0;

		$tar = static::$zombie_fraction ? $this : $combatant;
		if ($tar->distance() < $rng[0] || $tar->distance() > $rng[1]) return 0;
		if ($item::$energy_cost > $this->energy) return 0;
		
		$ammo = $item::ammo();
		foreach ($ammo as $type => $count) if ($type != "self" && $type != "custom") {
			if (Tool_System::instance_of($type, 'Model_Items_Abstract_Ammo')) {
				if ((!$belt = $this->inventory->get('Model_Items_Ammobelt'))) return 0;
				if ($belt[0]->has($type) < $count) return 0;
			} elseif (count($this->inventory->get($type)) < $count) return 0;
		} elseif ($type == "custom") if (!$item->has_ammo()) return 0;
		
		$dmg = $item::damage();
		
		$acc = ($item::$accuracy * $this->accuracy * ($item::$accuracy_type == Model_Battle_Weapon::MBW_ACC_STATIC) ? 1 : ((110 - $combatant->distance())/100));
		Tool_Numerics::bounds($acc, 0, 1);
		
		return ((($dmg[0]+$dmg[1])/2) * $acc)/(($item::$lock_type == Model_Battle_Weapon::MBW_LCK_PLAYER) ? ($item::$reload_time + 1) : ($item->lock + 1));
	}

    /**
     *
     * @param int $eff
     * @param Model_Battle_Weapon $item
     * @param Model_Battle_Combatant $combatant
     * @return float|int
     */
    private function waste($eff, $item, $combatant) {
        $waste = 0;
        switch ($item::$damage_type) {
            case Model_Battle_Weapon::MBW_DMG_IMPACT:
                if ($combatant->health() < $eff) $waste = ($eff - $combatant->health());
                break;
            case Model_Battle_Weapon::MBW_DMG_AREA:
                if ($combatant->stack_health() < $eff) $waste = ($eff - $combatant->stack_health());
                break;
            case Model_Battle_Weapon::MBW_DMG_SCATTER:
                $tmp = ($eff > $combatant->health()) ? ($eff/2 + $combatant->health()/2) : $eff;
                $waste = $eff - $tmp;
                if ($combatant->stack_health() < $tmp) $waste += ($tmp - $combatant->stack_health());
                break;
        }

        return $waste;
    }

    public function get_ddshift($distance) {
        return array(1,0);
    }

    /**
     *
     * @param Model_Battle_Weapon $item
     * @param Model_Battle_Combatant $combatant
     * @return float|null
     */
    private function efficiency($item, $combatant) {
        if (($eff = $this->potential($item, $combatant)) <= 0)
            return null;

        $waste = $this->waste($eff, $item, $combatant);
        list($a,$b) = $this->get_ddshift($combatant->distance());
        return $a * $eff - $b * $waste;
    }

    protected function filter_weapons() {
        $items = $this->inventory->get('Model_Battle_Weapon');
        $items[] = static::$unarmed;
        return $items;
    }

    protected function what_to_do($combatants) {
        $ret_item = null;
        $ret_combatant = null;
        $ret_array = array();
        $ceff = -PHP_INT_MAX;

        $items = $this->filter_weapons();

        foreach ($items as $item) foreach ($combatants as $combatant)
            if (($eff = $this->efficiency($item, $combatant)) !== null) {
                $ret_array[] = array($item,$combatant);
                if ($eff > $ceff) {
                    $ret_combatant = $combatant;
                    $ret_item = $item;
                    $ceff = $eff;
                }
            }

        if ($ret_item === null || !count($ret_array))
            return null;
        elseif ($this->is_zombie()) {
            shuffle($ret_array);
            return $ret_array[0];
        } else return array($ret_item,$ret_combatant);
    }

    /**
     *
     * @param Model_Battle_Combatant $combatant
     * @return boolean
     */
	public function attack($combatants) {
		if (!$combatants) return false;
        if ($this->count <= 0) return true;

		$note = Array();
		
		if ($this->lock > 0) {
			$this->lock--;
			return true;
		}

        if (($intelligence = $this->what_to_do($combatants)) === null)
            return false;

        list($item, $combatant) = $intelligence;

        /** @var $item Model_Battle_Weapon */
		if ($item::$reload_time > 0) {
			switch ($item::$lock_type) {
				case Model_Battle_Weapon::MBW_LCK_WEAPON:
					if ($item->lock > 0) {
						$item->lock--;
						return true;
					} else $item->lock = $item::$reload_time;
					break;
				case Model_Battle_Weapon::MBW_LCK_PLAYER:
					$this->lock = $item::$reload_time;
					break;
			}
		}	
		
		$acc = ($item::$accuracy * $this->accuracy * ($item::$accuracy_type == Model_Battle_Weapon::MBW_ACC_STATIC) ? 1 : ((110 - $combatant->distance())/100));
		Tool_Numerics::bounds($acc, 0, 1);
		
		$dmg = 0;
		$missed = true;
		for ($i = 0; $i < $this->count; $i++)
			if (mt_rand(0, 1000)/1000 > $acc) $dmg += 0;
			else
			{
				$missed = false;
				$dmg_rng = $item::damage();
				$dmg += mt_rand($dmg_rng[0], $dmg_rng[1]) * $this->damage_mod;
			}
		
		if ($missed) $note[] = 'Verfehlt!';
			
		switch ($item::$damage_type) {
			case Model_Battle_Weapon::MBW_DMG_IMPACT:
				if ($combatant->health() < $dmg) $dmg = $combatant->health();
				break;
			case Model_Battle_Weapon::MBW_DMG_SCATTER:
				if ($combatant->health() < $dmg) $dmg = $dmg/2 + $combatant->health()/2;
				break;
		}
		
		$ammo = $item::ammo();
		foreach ($ammo as $type => $count) if ($type != "self" && $type != "custom") {
			if (Tool_System::instance_of($type, 'Model_Items_Abstract_Ammo')) {
				/** @var $belt Model_Items_Ammobelt[] */
                $belt = $this->inventory->get('Model_Items_Ammobelt');
				$belt[0]->get($type, $count);
			} else {
				$group = $this->inventory->get($type);
				for ($i = 0; $i < $count; $i++) $group[$i]->consume(); 
			}
		} elseif ($type == "self") $item->consume();
		else /** @noinspection PhpUndefinedMethodInspection */
            $item->consume_ammo();
		
		$this->energy -= $item::$energy_cost;

        $armor = null;
        $cover = array();
        $slot = Model_Items_Abstract_Armor::get_random_slot();
        foreach ($combatant->inventory->get('Model_Items_Abstract_Armor') as $pitem)
            /** @var Model_Items_Abstract_Armor $pitem */
            if ($pitem->blocks($slot))
                $armor = $pitem;
            elseif ($pitem->covers($slot))
                $cover[] = $pitem;

        if ($armor || $cover) {
            $protect = 0;
            foreach ($cover as $citem)
                /** @var $citem Model_Items_Abstract_Armor */
                $protect += ($dmg - $citem->take_damage($dmg));

            $dmg -= $protect;
            if ($armor) {
                $tmp = $armor->take_damage($dmg);
                $protect += ($dmg - $tmp);
                $dmg = $tmp;
            }

            foreach ($cover as $citem)
                if ($citem->is_destroyed()) {
                    $note[] = 'Schutz zerstört!';
                    break;
                }

            if ($armor && $armor->is_destroyed())
                $note[] = 'Rüstung zerstört!';
        } else $protect = null;

        if (mt_rand(0,100) > ($item::$durability * 100))
        {
            $item->consume();
            $note[] = 'Waffe zerstört!';
        }
		$kills = $combatant->damage($dmg);

		$this->log_entry(new Model_Log_Types_Battle_Atk($this, $combatant, $item, $dmg, $kills, implode(', ', $note), $armor, $cover, $protect));
		
		return (!$kills) ? true : $kills;
	}
	
	public function move() {
		if ($this->distance > 0) {		
			$this->distance -= static::$speed;
			Tool_Numerics::bounds($this->distance, 0, 100);
		}
	}

    public function finalize() {
        foreach ($this->inventory->get('Model_Battle_Weapon') as $item)
            /** @var Model_Battle_Weapon $item */
            $item->lock = 0;

        foreach ($this->inventory->get('Model_Items_Abstract_Armor') as $item)
            /** @var Model_Items_Abstract_Armor $item */
            if ($item->is_destroyed()) {
                if ($cls = $item->get_destroyed_class())
                    $this->inventory->add(new Model_Items_Generic_Clothes());
                $item->consume();
            }
    }
}