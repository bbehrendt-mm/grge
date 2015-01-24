<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_String extends Model_Log_Message {

	protected static $type = Model_Log_Message::MLM_PRERENDERED_STRING;

	private $var = Array();
	
	/**
	 * Creates a simple text message
	 * @param String $title Short message title
	 * @param String $body Message body
	 * @param Array $variables Variables
	 */
	public function __construct($title, $body, $variables = []) {

		parent::__construct([
			'title' => $title,
			'body' => $body
		]);
		
		$this->var = $variables;
	}

	public function as_notification() {
		$d = $this->postprocess($this->data);
		return [
			'info',
			$d['body'],
			$d['title'] ? $d['title'] : null,
		];
	}

	protected function postprocess($data) {
		if ($this->var === true) return $data;
		$tmp = [];

		$this->var;
		foreach ($this->var as $key => $value)
			$tmp[$key] = is_array($value) ? __($value[0]) :$value;

		$data['title'] = __($data['title'], $tmp);
		$data['body'] = __($data['body'], $tmp);
		return $data;
	}
}