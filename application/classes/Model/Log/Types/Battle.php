<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle extends Model_Log_Message {
	
	private $title;
	/** @var Model_Log_Message[][] $log */
	private $log;
	private $var = Array();
	private $trv = Array();

	protected static $type = Model_Log_Message::MLM_BATTLE_CONTAINER;

	public function __construct($title, $log, $variables = Array(), $translateables = Array()) {
		$this->title = $title;
		$this->log = $log;
		
		$this->var = $variables;
		$this->trv = $translateables;
		
		parent::__construct([]);
	}

	/**
	 * Translates a text using translateables and variables
	 * @param string $s
	 * @return string
	 */
	private function translate($s) {
		if ($this->var === true) return $s;
		$tmp = $this->var;
		foreach ($this->trv as $key => $value)
			$tmp[$key] = __($value);
		return __($s, $tmp);
	}

	protected function postprocess($data) {
		$battle = [];
		foreach ($this->log as $round => $messages) {
			if ($round > 0 && $messages)
				$battle[] = (new Model_Log_Types_Battle_Round($round))->render(true);
			foreach ($messages as $message )
				if (is_object($message) && Tool_System::instance_of($message,'Model_Log_Message'))
					$battle[] = $message->render(true);
		}

		return [
			'text' => $this->translate($this->title),
			'battle' => $battle,
		];
	}
}