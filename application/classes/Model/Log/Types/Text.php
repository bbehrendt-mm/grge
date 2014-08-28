<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Text extends Model implements Interface_Message {
	
	private $title;
	private $head;
	private $body;
	private $effect;
	
	private $var = Array();
	private $trv = Array();
	
	private $timecode;
	
	/**
	 * Creates a simple text message
	 * @param String $title Short message title
	 * @param String $head Message headline; will be used as title if no headline is provided
	 * @param String $body Message body
	 * @param Array $variables Variables
	 * @param Array $translateables Variables that need to be translated
	 */
	public function __construct($title, $head, $body, $variables = Array(), $translateables = Array()) {
		$this->title = $title;
		$this->head = $head;
		$this->body = $body;
		
		$this->var = $variables;
		$this->trv = $translateables;
		
		$this->timecode = time();
	}
	
	/**
	 * Translates a text using translateables and variables
	 * @param unknown $s
	 */
	private function translate($s) {
		if ($this->var === true) return $s;
		$tmp = $this->var;
		foreach ($this->trv as $key => $value)
			$tmp[$key] = __($value);
		return __($s, $tmp);
	}
	
	public function render_title() {
		return $this->translate($this->title ? $this->title : ($this->head ? $this->head : NULL));
	}
	
	public function render_body() {
		$tmp = ($this->head) ? "<b>" . $this->translate($this->head) . "</b><br />" : '';
		$tmp .= ($this->body) ? "<div class='message_body'>" . $this->translate($this->body) . "</div>" : '';
		$tmp .= ($this->effect) ? "<div class='message_effects'>" . $this->translate($this->body) . "</div>" : '';
		
		return ($tmp == '') ? null : $tmp;
	}
	
	public function timecode() {
		return $this->timecode;
	}

    /**
     * @param Interface_Message $new
     * @return bool
     */
    public function merge($new) {
        return false;
    }
}