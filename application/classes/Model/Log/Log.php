<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Log extends Model {

    /**
     * @var Interface_Message[] messages
     */
    private $messages;
	private $new = [];
	private $max_length;
	
	/**
	 * Constructs a log
	 * @param int|null $max_length Maximum buffer size, once this size is reached older messages will be removed; can be omitted or set null if auto-deletion is undesired
	 */
	public function __construct($max_length = null) {
		$this->max_length = $max_length;
		$this->messages = Array();
	}

	private function unread($set = null) {
		if (!isset($this->new[Globals::PrimaryPlayerF()->id()]))
			$this->new[Globals::PrimaryPlayerF()->id()] = 0;

		if ($set !== null)
			return $this->new[Globals::PrimaryPlayerF()->id()] = $set;
		else return $this->new[Globals::PrimaryPlayerF()->id()];
	}
	
	/**
	 * Clears all stores messages
	 */
	public function clear() {
		$this->messages = Array();
		$this->new = [];
	}

    /**
     * Adds a new message to buffer
     *
     * @param Interface_Message|String $new_message
     * @param array                    $variables
     * @param array                    $translateables
     *
     * @throws Exception
     */
	public function add($new_message, $variables = [], $translateables = []) {
		if (is_string($new_message)) {
			foreach ($translateables as $k => $v)
				$variables[$k] = [$v];
            $this->add(new Model_Log_Types_String(null,$new_message,$variables));
        } elseif (Tool_System::instance_of($new_message, 'Model_Log_Message')) {

            if (count($this->messages) == 0 || !$this->messages[count($this->messages)-1]->merge($new_message))
                $this->messages[] = $new_message;

			foreach ($this->new as &$counter) $counter++;
			unset($counter);

            //Delete oldest messages once counter is reached
            while($this->max_length > 0 && count($this->messages) > $this->max_length) array_shift($this->messages);
        }

	}
	
	/**
	 * Trims log length to $length
	 * @param number $length Length to trim message log to
	 */
	public function trim($length) {
		while(count($this->messages) > $length) array_shift($this->messages);
		foreach ($this->new as &$counter)
			$counter= min($counter, count($this->messages));
	}
	
	/**
	 * Returns all message instances
	 * @param bool $reverse True if you want LIFO ordering; default is FIFO (false)
	 * @return Model_Log_Message[]
	 */
	public function get_all($reverse = false) {
		return $reverse ? array_reverse($this->messages) : $this->messages;
	}
	
	/**
	 * Gets only new messages
	 * @param bool $reverse True if you want LIFO ordering; default is FIFO (false)
	 * @return Model_Log_Message[]
	 */
	public function get_new($reverse = false) {
		$ret = array_slice(array_reverse($this->messages), 0, $this->unread());
		return $reverse ?  $ret : array_reverse($ret);
	}
	
	/**
	 * Gets only old messages
	 * @param bool $reverse True if you want LIFO ordering; default is FIFO (false)
	 * @return Model_Log_Message[]
	 */
	public function get_old($reverse = false) {
		$ret = array_slice(array_reverse($this->messages), $this->unread());
		return $reverse ?  $ret : array_reverse($ret);
	}

	/**
	 * Resets news counter
	 */
	public function reset_news_counter() {
		$this->unread(0);
	}
}