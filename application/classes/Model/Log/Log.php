<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Log extends Model {

    /**
     * @var Interface_Message[] messages
     */
    private $messages;
	private $new = 0;
	private $max_length;
	
	/**
	 * Constructs a log
	 * @param int|null $max_length Maximum buffer size, once this size is reached older messages will be removed; can be omitted or set null if auto-deletion is undesired
	 */
	public function __construct($max_length = null) {
		$this->max_length = $max_length;
		$this->messages = Array();
	}
	
	/**
	 * Clears all stores messages
	 */
	public function clear() {
		$this->messages = Array();
		$this->new = 0;
	}

	/**
	 * Adds a new message to buffer
	 * @param Interface_Message|String $new_message
	 * @param array $variables
	 * @param array $translateables
	 */
	public function add($new_message, $variables = [], $translateables = []) {
		if (is_string($new_message)) {
            $this->add(new Model_Log_Types_Text(null,null,$new_message,$variables,$translateables));
        } else {

            if (count($this->messages) == 0 || !$this->messages[count($this->messages)-1]->merge($new_message))
                $this->messages[] = $new_message;

            $this->new++;

            //Delete oldest messages once counter is reached
            while($this->max_length > 0 && count($this->messages) > $this->max_length) array_shift($this->messages);
        }

	}

	/**
	 * Resets news counter
	 */
	public function reset_news_counter() {
		$this->new = 0;
	}
	
	/**
	 * Trims log length to $length
	 * @param number $length Length to trim message log to
	 */
	public function trim($length) {
		while(count($this->messages) > $length) array_shift($this->messages);
		$this->new = min($this->new, count($this->messages));
	}
	
	/**
	 * Returns all message instances
	 * @param bool $reverse True if you want LIFO ordering; default is FIFO (false)
	 * @return Interface_Message Message[] instances
	 */
	public function get_all($reverse = false) {
		return $reverse ? array_reverse($this->messages) : $this->messages;
	}
	
	/**
	 * Gets only new messages
	 * @param bool $reverse True if you want LIFO ordering; default is FIFO (false)
	 * @return Interface_Message Message[] instances
	 */
	public function get_new($reverse = false) {
		$ret = array_slice(array_reverse($this->messages), 0, $this->new);
		return $reverse ?  $ret : array_reverse($ret);
	}
	
	/**
	 * Gets only old messages
	 * @param bool $reverse True if you want LIFO ordering; default is FIFO (false)
	 * @return Interface_Message[] Message instances
	 */
	public function get_old($reverse = false) {
		$ret = array_slice(array_reverse($this->messages), $this->new);
		return $reverse ?  $ret : array_reverse($ret);
	}
	
	/**
	 * Renders all titles and returns them as array
	 * @return Array All titles as string
	 */
	public function render_titles() {
		return array_map(
            function($obj) {
                /**
                 * @var $obj Model_Log_Types_Text
                 */
                return $obj->render_title();
            }, $this->messages);
	}
	
	/**
	 * Renders all bodies and returns them as array
	 * @return string[] All bodies as string
	 */
	public function render_bodies() {
		return array_map(
            function($obj) {
                /** @var $obj Model_Log_Types_Text */
                return $obj->render_body();
            }, $this->messages);
	}
}