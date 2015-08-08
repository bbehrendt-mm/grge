<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Raw extends Model_Log_Message {

	protected static $type = Model_Log_Message::MLM_RAW_DATA;

	public function __construct($data) {

		parent::__construct([
			'title' => 'System Output',
			'body' => is_string($data) ? $data : print_r($data, true)
		]);

	}
}