<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Gamelayer_Logic extends Model_Gamelayer_Io {

	public const MGLS_Health =  1;
	public const MGLS_Energy =  2;
	public const MGLS_Hunger =  4;
	public const MGLS_Thirst =  8;
	public const MGLS_Drunk  = 16;
	public const MGLS_Sleepy = 32;

    public function recalculate_flow(): void
    {
        if ($this->timeflow() !== 1) return;
        $steps = array(15,30,60,120,300,600,900);

        $sum = 0;
        $players = $this->players(true);

        if (count($players) === 0)
            return;

        foreach ($players as $p)
            $sum += $p->vote_time();

        //Reflow
        $this->reflow_ticks($steps[(int)round($sum/count($players))]);
    }

    /**
     * @param number $location
     * @param Model_Combat_Actor $obj
     */
    public function register_ghul($location, $obj): void
    {
        $this->set['gamedata']->ghuls[] = array('location' => $location, 'data' => $obj);
    }

    public function unregister_ghul($id): void
    {
        unset($this->set['gamedata']->ghuls[$id]);
    }

    /**
     * @param $lid
     *
     * @return Model_Combat_Zombies_Ghul[]
     * @throws Exception
     */
    public function get_ghuls($lid): array
    {
        $ret = [];

        foreach ($this->set['gamedata']->ghuls as $k => $gob)
            if (random_int(0,100) < (($gob['location'] === $lid) ? 30 : 5)) {
                $ret[$k] = $gob['data'];
                break;
            }

        return $ret;
    }

	final public function mass_consume($data, $callbacks = NULL): bool
    {
		if ($data === NULL) return false;
			
		//Check, if all items are available
		foreach ($data as $class => $count)
			if (Tool_Scripts::count_available_items($class) < $count) return false;
		
		//Callbacks
		if ($callbacks)
			foreach ($data as $class => $count) if (isset($callbacks[$class]))
			{
				$tempcount = 0;
				foreach (Tool_Scripts::available_items($class) as $item) if ($count > 0)
					if ($callbacks[$class]($item)) $tempcount++;
				if ($tempcount < $count) return false;
			}	
		
		//Consume items
		foreach ($data as $class => $count)
			if (Tool_System::instance_of($class, Model_Items_Abstract_Ammo::cls())) {
				/** @var $belt Model_Items_Ammobelt[] */
                if (!$belt = Tool_Scripts::available_items(Model_Items_Ammobelt::cls())) return false;
				$belt[0]->get($class, $count);
			}
			else foreach (Tool_Scripts::available_items($class) as $item) if ($count > 0) if (!isset($callbacks[$class])
                || ($callbacks !== null && isset($callbacks[$class])
                    && $callbacks[$class]($item))
            )
			{
				$item->consume();
				$count--;
			}
			
		return true;
	}

    /**
     * Returns TRUE, if given UIN can be used as an item (based on the inventory the item currently resides in)
     *
     * @param number             $uin
     * @param Interface_Plentity $perspective
     *
     * @return bool
     * @throws Exception
     */
	final public function item_available($uin, $perspective = null): bool
    {
        $at_loc_rooms = false;
        if ($this->location()) foreach ($this->location()->rooms() as $r) if ($r->enabled())
            if ($r->inventory()->has($uin)) {
                $at_loc_rooms = true;
                break;
            }

		//Check player inventory and location inventory
		return (
            Globals::CurrentPlayerF()->inventory()->has($uin) ||
            $at_loc_rooms ||
            ($this->location() ? $this->location()->inventory()->has($uin) : false) ||
            ($perspective && $perspective->allow(Interface_Plentity::IC_ALLOW_SHOW_INVENTORY)));
	}
}
