<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Class Model_Log_Types_Text
 * @deprecated
 */
class Model_Log_Types_Text extends Model_Log_Message {

	protected static $type = Model_Log_Message::MLM_PRERENDERED_STRING;

	private $var = Array();
	private $trv = Array();
	
	/**
	 * Creates a simple text message
	 * @param String $title Short message title
	 * @param String $head Message headline; will be used as title if no headline is provided
	 * @param String $body Message body
	 * @param Array $variables Variables
	 * @param Array $translateables Variables that need to be translated
	 */
	public function __construct($title, $head, $body, $variables = Array(), $translateables = Array()) {

		parent::__construct([
			'title' => $title,
			'body' => $body
		]);
		
		$this->var = $variables;
		$this->trv = $translateables;
	}

	public function as_notification() {
		$d = $this->postprocess($this->data);
		return [
			'info',
			$d['body'],
			isset($d['title']) ? $d['title'] : null,
		];
	}

	protected function postprocess($data) {
		if ($this->var === true) return $data;
		$tmp = $this->var;
		foreach ($this->trv as $key => $value)
			$tmp[$key] = __($value);

		if ($data['title'])
			$data['title'] = __($data['title'], $tmp);
		else unset($data['title']);
		$data['body'] = __($data['body'], $tmp);
		return $data;
	}
}