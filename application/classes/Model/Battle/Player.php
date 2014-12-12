<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Battle_Player extends Model_Battle_Combatant {
	
	private $pid;
	
	protected static $max_health = 100;
	protected $health;
	protected static $max_energy = 100;
	protected $energy;
	
	protected static $zombie_fraction = false;
	protected $distance = 0;
	
	protected $lock = 0;
	protected static $speed = 1;
	
	protected static $unarmed = 'Model_Battle_Unarmed_Fist';
	
	private $fist_kill = false;
	
	/**
	 * 
	 * @var Model_Inventory
	 */
	protected $inventory;
	protected $name;
	protected $count;
	
	protected function sync() {
		global $game;
		$game->get_player($this->pid)->set_cod("Von Zombies gefressen");
		$game->get_player($this->pid)->stats_set(Model_Player::MP_STAT_HEALTH, $this->health, Model_Player::MP_STAT_ENERGY, $this->energy);
		$game->get_player($this->pid)->set_cod(null);
	}
	
	public function __construct($pid) {
		global $game;
		
		$this->pid = $pid;
		$this->name = $game->get_player($pid)->name();
		$this->health = $game->get_player($this->pid)->stats_get(Model_Player::MP_STAT_HEALTH);
		$this->energy = $game->get_player($this->pid)->stats_get(Model_Player::MP_STAT_ENERGY);
		$this->evasiveness = $game->get_player($this->pid)->stats_get(Model_Player::MP_CHAR_EVASIVENESS);
		$this->accuracy = $game->get_player($this->pid)->stats_get(Model_Player::MP_CHAR_ACCURACY);
		$this->resistance_mod = $game->get_player($this->pid)->stats_get(Model_Player::MP_CHAR_DAMAGE_RESISTANCE);
        $this->damage_mod = $game->get_player($this->pid)->stats_get(Model_Player::MP_CHAR_DAMAGE_MULTIPLIER);
		
		$c = $game->get_player($pid)->inventory();
		
		parent::__construct($c, 0, $game->get_player($pid)->name(), 1);
	}
	
	public function damage($dmg) {
		global $game;
		
		$r = parent::damage($dmg);
		$this->sync();

        $injury = mt_rand(0,100);

		if (!$game->get_player($this->pid)->buff_retr('blood') && $injury < $dmg/2) {
			$buff = new Model_Buffs_Blood($this->pid);
			$this->log_entry(new Model_Log_Types_Battle_Injury($game->get_player($this->pid)->name(), $buff->icon(), $buff->name()));
        }
        if ($injury < $dmg*5) {
            $buff = new Model_Buffs_Bite($this->pid, ceil($dmg/2));
            $this->log_entry(new Model_Log_Types_Battle_Injury($game->get_player($this->pid)->name(), $buff->icon(), $buff->name()));
        }
		
		return $r;
	}
	
	public function attack($combatants) {
		global $game;
		$k = parent::attack($combatants);
		
		if ($k !== false && $k !== true) $game->get_player($this->pid)->achievements()->achieve(Model_Achievement::MA_KILLED_ZOMBIES, $k);
		if ($this->fist_kill) {
			$game->get_player($this->pid)->achievements()->achieve(Model_Achievement::MA_FIST_FIGHT, $k);
			$this->fist_kill = false;
		}
		$this->sync();		
	}

    protected function what_to_do($combatants) {
        $intelligence = parent::what_to_do($combatants);
        if ($intelligence === null)
            return null;

        list($item, ) = $intelligence;
        if ($item == static::$unarmed) $this->fist_kill = true;
        return $intelligence;
    }
	
	public function escape() {
		global $game;
		
		$game->get_player($this->pid)->achievements()->achieve(Model_Achievement::MA_CHICKEN);
		return true;
	}

    public function get_ddshift($distance) {
        global $game;

        $battle_settings = $game->get_player($this->pid)->get_battle_settings();

        if ($distance > 50)
            switch ($battle_settings[Model_Player::MP_SETTINGS_BATTLE_DISTANCE_DAMAGE_SHIFT]) {
                case 1: return array(1,1);
                case 2: return array(3,1);
                case 3: return array(5,1);
                default: return array(1,0);
            }
        elseif ($distance > 20)
            switch ($battle_settings[Model_Player::MP_SETTINGS_BATTLE_DISTANCE_DAMAGE_SHIFT]) {
                case 1: return array(2,1);
                case 2: return array(3.5,1);
                case 3: return array(5,1);
                default: return array(1,0);
            }
        elseif ($distance > 10)
            switch ($battle_settings[Model_Player::MP_SETTINGS_BATTLE_DISTANCE_DAMAGE_SHIFT]) {
                case 1: return array(5,0);
                case 2: return array(5,0.5);
                case 3: return array(5,1);
                default: return array(1,0);
            }
        else switch ($battle_settings[Model_Player::MP_SETTINGS_BATTLE_DISTANCE_DAMAGE_SHIFT]) {
            case 1: return array(10,0);
            case 2: return array(7.5,0.5);
            case 3: return array(5,1);
            default: return array(1,0);
        }
    }

    protected function filter_weapons() {
        global $game;

        $items = array();
        $battle_settings = $game->get_player($this->pid)->get_battle_settings();

        foreach (parent::filter_weapons() as $item) {
            /** @var $item Model_Battle_Weapon */
            if ($battle_settings[Model_Player::MP_SETTINGS_BATTLE_NOENERGY] && ($item::$energy_cost > 0)) continue;

            $ammo = $item::ammo();
            if ($battle_settings[Model_Player::MP_SETTINGS_BATTLE_NOSELFAMMO] && isset($ammo['self'])) continue;
            if ($battle_settings[Model_Player::MP_SETTINGS_BATTLE_NOTANKAMMO] && isset($ammo['custom'])) continue;
            foreach ($ammo as $key => $value) if ($value > 0 && isset($battle_settings[$key]) && $battle_settings[$key])
                continue(2);

            $items[] = $item;
        }

        return $items;
    }
}