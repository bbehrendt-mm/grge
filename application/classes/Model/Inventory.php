<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Inventory extends Model {
	
	private $data = array();
	private $limit;
	private $t_limit;
	private $current;
    private $current_full;
    private $carrier_inventory;

	//Create inventory, set weight limit
	public function __construct($weight_limit = NULL, $carrier = false) {
		$this->limit = $weight_limit;
        $this->carrier_inventory = $carrier;
	}

    /**
     * Prefetches all items in this inventory
     */
    public function prefetch(): void
    {
        Globals::CurrentGameF()->uin()->prefetch(array_keys($this->data));
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
    public function temporal_limit($limit): void
    {
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
    public function reset_weight(): void
    {
		$this->current = 0;
		foreach (array_keys($this->data) as $uin) {
            /** @var Model_Items_Abstract_Item $item */
		    $item = Globals::CurrentGameF()->uin()->get($uin);

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
     *
     * @param Model_Items_Abstract_Item $item
     *
     * @return bool True, when the item is added, otherwise false
     * @throws Exception
     */
    public function add($item): bool {
		if (!Tool_System::instance_of($item, Model_Items_Abstract_Item::cls())) return false;
		if ($this->limit() && !($this->carrier_inventory && $item->is_carrier_item()) && ($this->current + $item->weight() > $this->limit())) return false;

		$max = $item->get_max_per_player();
		if ($this->carrier_inventory && ($max > 0) && (count($this->get(get_class($item))) >= $max))
            return false;
		
		if (!$item->uin()) Globals::CurrentGameF()->uin()->set($item);
		
		$this->data[$item->uin()] = true;
		$this->reset_weight();
		return true;
    }

    /**
     * Removes an item from the inventory
     * @param $uin number
     * @return Model_Items_Abstract_Item|bool
     * @throws Exception
*/
    public function remove($uin) {
		if (!isset($this->data[$uin])) return false;
		
		unset($this->data[$uin]);
		
		$this->reset_weight();
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Globals::CurrentGameF()->uin()->get($uin);
    }

    /**
     * Returns all items, or those of a specified class
     * @param string|null $item_class Item class, omit to return all items in this inventory
     * @return Model_Items_Abstract_Item[]
     * @throws Exception
*/
    public function get($item_class = NULL): array
    {
		$ret = array();
		foreach (array_keys($this->data) as $uin) 
		{
			$item = Globals::CurrentGameF()->uin()->get($uin);
			if ($item) 
			{
				if ($item_class === NULL && Tool_System::instance_of($item, Model_Items_Abstract_Virtual::cls()))
                    continue;
                if ($item_class === NULL || ($item instanceof $item_class) || in_array($item_class, class_implements($item), true))
                    $ret[] = $item;
			} else $this->remove($uin);
		}
		return $ret;
    }

    /**
     * Returns weather an item with the specified UIN is in this inventory
     * @param number $uin
     * @return bool
     * @throws Exception
*/
    public function has($uin): bool
    {
		return (isset($this->data[$uin]) && Globals::CurrentGameF()->uin()->get($uin));
	}

    public function grind(): void
    {
        foreach (array_keys($this->data) as $uin)
            Globals::CurrentGameF()->uin()->remove($uin);
    }
}	