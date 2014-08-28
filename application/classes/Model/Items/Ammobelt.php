<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Ammobelt extends Model_Items_Abstract_Item {

	protected static $static_info = Array(
			'name' => 'Munitionsgürtel',
			'icon' => 'ammobelt',
			'description' => 'Dieses kleidsame Accessoir ist unerlässlich für den modebewussten Überlebenskünstler. Gefertigt von geschickten Kinderhänden aus irgend einem Drittweltland und aus 100% Zombieleder garantiert dieser Munitionsgürtel volle Übersicht und schnellen Zugriff auf alle Arten von Munition.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 0;
	protected static $essential = true;
	protected static $associated_view = 'ammobelt';
	
	private $content = Array();
	
	public function __construct() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;
			
		parent::__construct();
		
		$this->content['Model_Items_Battery'] = mt_rand($game->config('items.ammobelt.startup_bat.min'), $game->config('items.ammobelt.startup_bat.max'));
		$this->content['Model_Items_Ammo'] = mt_rand($game->config('items.ammobelt.startup_blt.min'), $game->config('items.ammobelt.startup_blt.max'));
		
		//JOB BONUS Soldier
		if ($player->job(1020)) switch ($player->job(false))
		{
			case 4: $this->content['Model_Items_Ammo'] += mt_rand(5, 10); break;
			case 5: $this->content['Model_Items_Ammo'] += mt_rand(10, 20); break;
			default: $this->content['Model_Items_Ammo'] += mt_rand(0, 3); break;
		}
	}
	
	public function consume() {
		return false;
	}
	
	public function grind() {
		return false;
	}
	
	/**
	 * Adds a stack of items to the belt
	 * @param Model_Items_Abstract_Ammo $item Stack
	 */
	public function add($item) {
		if (!(Tool_System::instance_of($item, 'Model_Items_Abstract_Ammo'))) return;
		
		if (!isset($this->content[get_class($item)])) $this->content[get_class($item)] = $item->count();
		else $this->content[get_class($item)] += $item->count();
		
		$item->grind();
		return;
	}
	
	/**
	 * Returns the stack size for item_class stored in this belt
	 * @param string $item_class
     * @return int
     */
	public function has($item_class) {
		if (!isset($this->content[$item_class])) return 0;
		else return $this->content[$item_class];
	}
	
	public function contains() {
		$ret = Array();
		foreach ($this->content as $class => $value)
            /** @var $class string|Model_Items_Abstract_Item */
            if ($value > 0)
                $ret[$class::static_icon()] = $value;
		
		return $ret;
	}

    /**
     * Takes a number of item_class out of the belt
     * @param string $item_class
     * @param int $count
     * @return bool
     */
	public function get($item_class, $count = 1) {
		if ($count == 0) return true;
		elseif ($count < 0) return false;
		elseif ($this->has($item_class) < $count) return false;
		else {
			$this->content[$item_class] -= $count;
			return true;
		}
	}
	
	public function drop() {
        /**
         * @global $player Model_Player
         */
		global $player;
		$player->log()->add(new Model_Log_Types_Text(null, null, 'Du solltest deinen Munitionsgürtel nicht aus der Hand geben ...'));
		return false;
	}
	
	public function drop_dead() {
		$ret = array();
		foreach ($this->content as $type => $count)
			$ret[] = new $type($count);
		return $ret;
	}

    public function interaction_ammodrop($arg) {
        /**
         * @global $player Model_Player
         */
        global $player;

        if (!is_array($arg) || !isset($arg['type']) || !isset($arg['count']))
            return;

        if ((int)$arg['count'] <= 0) return;

        foreach ($this->content as $class => $value)
            if (md5($class::static_icon()) == $arg['type']) {
                if ($this->get($class, (int)$arg['count']))
                    $player->location()->inventory()->add(new $class((int)$arg['count']));
                else $player->log()->add(new Model_Log_Types_Text(null, null, 'Soviele hast du nicht dabei.'));
                break;
            }

    }
}	