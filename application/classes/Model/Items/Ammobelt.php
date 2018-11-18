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
	
	private $content = Array();
	
	public function __construct() {
		parent::__construct();
		
		$this->content[Model_Items_Battery::cls()] = random_int(Globals::CurrentGameF()->config('items.ammobelt.startup_bat.min'), Globals::CurrentGameF()->config('items.ammobelt.startup_bat.max'));
		$this->content[Model_Items_Ammo::cls()] = random_int(Globals::CurrentGameF()->config('items.ammobelt.startup_blt.min'), Globals::CurrentGameF()->config('items.ammobelt.startup_blt.max'));
		
		//JOB BONUS Soldier
		if (Globals::CurrentPlayerActualF() && Globals::CurrentPlayerActualF()->job(1020)) switch (Globals::CurrentPlayerActualF()->job(false))
		{
			case 4: $this->content[Model_Items_Ammo::cls()] += random_int(5, 10); break;
			case 5: $this->content[Model_Items_Ammo::cls()] += random_int(10, 20); break;
			default: $this->content[Model_Items_Ammo::cls()] += random_int(0, 3); break;
		}
	}
	
	public function consume(): void {}
	
	public function grind(): void {}
	
	/**
	 * Adds a stack of items to the belt
	 * @param Model_Items_Abstract_Ammo $item Stack
	 */
	public function add($item): void
    {
		if (!Tool_System::instance_of($item, Model_Items_Abstract_Ammo::cls())) return;
		
		if (!isset($this->content[get_class($item)])) $this->content[get_class($item)] = $item->count();
		else $this->content[get_class($item)] += $item->count();
		
		$item->grind();
	}
	
	/**
	 * Returns the stack size for item_class stored in this belt
	 * @param string $item_class
     * @return int
     */
	public function has($item_class): int {
		if (!isset($this->content[$item_class])) return 0;
		else return $this->content[$item_class];
	}
	
	public function contains(): array
    {
		$ret = Array();
		foreach ($this->content as $class => $value)
            if ($value > 0)
                $ret[$class] = $value;
		
		return $ret;
	}

    /**
     * Takes a number of item_class out of the belt
     * @param string $item_class
     * @param int $count
     * @return bool
     */
	public function get($item_class, $count = 1): bool {
		if ($count === 0) return true;
		elseif ($count < 0) return false;
		elseif ($this->has($item_class) < $count) return false;
		else {
			$this->content[$item_class] -= $count;
			return true;
		}
	}
	
	public function drop($p = null, $silent = false): bool
    {
        if ($p && Tool_Scripts::is_npc($p)) return true;

		if (!$silent) Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Du solltest deinen Munitionsgürtel nicht aus der Hand geben ...'));
		return false;
	}
	
	public function drop_dead() {
		$ret = array();
		foreach ($this->content as $type => $count)
            if ($type !== Model_Items_Braincoin::cls())
			    $ret[] = new $type($count);
		return $ret;
	}
}	