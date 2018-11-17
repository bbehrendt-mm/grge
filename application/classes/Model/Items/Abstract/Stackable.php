<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Stackable extends Model_Items_Abstract_Item implements Interface_Countable {
	
	/**
	 * Maximum stack size; set 0 for unlimited
	 * @var int
	 */
	protected static $max_size = 0;
	
	/**
	 * Valid stack range for single find; used if no explicit size is given during construction.
	 * @var array
	 */
	protected static $autospawn = Array(1,1);
	
	/**
	 * Contains strings for automatic name extension; first array element is singular, second is plural
	 * @var array
	 */
	protected static $autoappender = Array('Element', 'Elemente');
	
	/**
	 * Current stack size
	 * @var int
	 */
	protected $count;

    /**
     * Stackable basic constructor;
     *
     * @param int $size
     *
     * @throws Exception
     */
	public function __construct($size = null) {
		//Parental constructor
		parent::__construct();
		
		//Set given size size or use autospawn
		if ($size !== null) $this->count = $size;
		else $this->count = random_int(static::$autospawn[0], static::$autospawn[1]);
	}
	
	/**
	 * Returns current stack size
	 * @return number
	 */
	public function count() {
		return $this->count;
	}
	
	/**
	 * Will consume $count elements (i.e. reduce stack count by $count)
	 * @param int $count Reduce by; default is 1
	 * @return int How many elements were actually consumed
	 * @see Model_Items_Abstract_Item::consume()
	 */
	public function consume($count = 1) {
		if ($this->count > $count) {	//We have enough elements to satisfy the demand
			$this->count -= $count;		//Reduce count
			return $count;				
		} else {						//We don't have enough elements to satisfy the demand
			parent::consume();			//Destroy stack
			return $this->count;		//Return how many elements were actually consumed
		}
	}
	
	public function stackname() {
		return static::$autoappender[($this->count == 1) ? 0 : 1];
	}
	
	/**
	 * Returns true when this object stack is full
	 * @return boolean
	 */
	public function is_stack_full() {
		return (static::$max_size > 0) && ($this->count >= static::$max_size);
	}

    /**
     * Returns max stack size
     * @return int
     */
    public function stack_max_size() {
        return static::$max_size;
    }

    /**
     * Will merge all stacks given by $target into this stack
     *
     * @param array $targets Target stacks to merge with; if omitted, the available_items script (default parameters) will be used
     *
     * @throws Exception
     */
	public function merge($targets = null) {
		//Do nothing if stack is already full
		if ($this->is_stack_full()) return;
		
		//Get targets if none were passed
		if ($targets === null) $targets = Tool_Scripts::available_items(get_class($this));
		
		//Iterate over all targets; do nothing if this stack or the target stack is full
		/** @var Model_Items_Abstract_Stackable $target */
        foreach ($targets as $target)
            if (!($this->uin() == $target->uin() || $this->is_stack_full() || $target->is_stack_full()))
			    $this->count += $target->consume( (static::$max_size == 0) ? PHP_INT_MAX : (static::$max_size - $this->count) );
	}
	
	/**
	 * Splits this stack and creates a new stack with a size of $splitval
	 * @param int $splitval
	 * @return Model_Items_Abstract_Stackable The created stack, or null if creating was not succesfull
	 */
	protected function split($splitval) {
		//Check if this stack is big enough to split
		if ($this->count <= $splitval) return null;
		
		//Reduce this stacks size
		$this->count -= $splitval;
		$me = get_class($this);
		
		//Return created value
		return new $me($splitval);
	}
}	