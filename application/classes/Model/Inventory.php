<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Inventory extends Model {
	
	private $data = array();
	private $limit = NULL;
	private $t_limit = NULL;
	private $current = NULL;
    private $current_full = NULL;
    private $carrier_inventory = null;

	//Create inventory, set weight limit
	public function __construct($weight_limit = NULL, $carrier = false) {
		$this->limit = $weight_limit;
        $this->carrier_inventory = $carrier;
	}

    /**
     * Prefetches all items in this inventory
     */
    public function prefetch() {
        Globals::CurrentGame()->uin()->prefetch(array_keys($this->data));
	}

    /**
     * Returns the current weight limit, or sets a new limit
     * @param null|number $newval New weight limit; omit to get the current limit
     * @return number
     */
    public function limit($newval = NULL) {
		if ($newval !== NULL) return $this->limit = $newval;
		else return ($this->t_limit !== NULL) ? min($this->t_limit,$this->limit) : $this->limit;
	}

    /**
     * Returns the current weight limit
     * @return number
     */
    public function global_limit() {
        return $this->limit;
    }

    /**
     * Sets a temporal weight limit to override the default limit
     * @param number $limit
     */
    public function temporal_limit($limit) {
		$this->t_limit = $limit;
	}

    /**
     * Returns the current weight of all items in this inventory combined
     * @param bool $ignore_carrier_state Count carrier items even if this is a carrier inventory
     * @return number
     */
    public function weight($ignore_carrier_state = false) {
        return  $this->limit ? min($this->current, $this->limit) : ($ignore_carrier_state ? $this->current_full : $this->current);
	}

    /**
     * Recalculates the current weight of this inventory
     */
    public function reset_weight() {
		$this->current = 0;
		foreach (array_keys($this->data) as $uin) {
            /** @var Model_Items_Abstract_Item $item */
		    $item = Globals::CurrentGame()->uin()->get($uin);

            if (!$item) {
                $this->remove($uin);
                continue;
            } elseif (!$this->carrier_inventory || !$item->is_carrier_item())
                $this->current += $item->weight();
            $this->current_full += $item->weight();
        }
	}

    /**
     * Adds a new item to this inventory. If it does not yet have an UIN, a new one will be generated for the item.
     * @param Model_Items_Abstract_Item $item
     * @return bool True, when the item is added, otherwise false
     */
    public function add(&$item) {
		if (!(Tool_System::instance_of($item, 'Model_Items_Abstract_Item'))) return false;
		if ($this->limit() && !($this->carrier_inventory && $item->is_carrier_item()) && ($this->current + $item->weight() > $this->limit())) return false;
        if ($this->carrier_inventory && ($item->get_max_per_player() > 0) && (count($this->get(get_class($item))) >= $item->get_max_per_player()))
            return false;
		
		if (!$item->uin()) Globals::CurrentGame()->uin()->set($item);
		
		$this->data[$item->uin()] = true;
		$this->reset_weight();
		return true;
	}

    /**
     * Removes an item from the inventory
     * @param $uin number
     * @return Model_Items_Abstract_Item|bool
     */
    public function remove($uin) {
		if (!isset($this->data[$uin])) return false;
		
		unset($this->data[$uin]);
		
		$this->reset_weight();
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Globals::CurrentGame()->uin()->get($uin);
	}

    /**
     * Returns all items, or those of a specified class
     * @param string|null $item_class Item class, omit to return all items in this inventory
     * @return Model_Items_Abstract_Item[]
     */
    public function get($item_class = NULL) {
		$ret = array();
		foreach (array_keys($this->data) as $uin) 
		{
			$item = Globals::CurrentGame()->uin()->get($uin);
			if ($item) 
			{
				if ($item_class === NULL && Tool_System::instance_of($item, 'Model_Items_Abstract_Virtual'))
                    continue;
                if (in_array($item_class, class_implements($item)) || ($item_class === NULL) || ($item instanceof $item_class)) $ret[] = $item;
			} else $this->remove($uin);
		}
		return $ret;
	}

    /**
     * Returns weather an item with the specified UIN is in this inventory
     * @param number $uin
     * @return bool
     */
    public function has($uin) {
		return (isset($this->data[$uin]) && Globals::CurrentGame()->uin()->get($uin));
	}

    public function grind() {
        foreach (array_keys($this->data) as $uin)
            Globals::CurrentGame()->uin()->remove($uin);
    }
}	