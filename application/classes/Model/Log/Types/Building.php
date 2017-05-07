<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Building extends Model_Log_Message {

    protected static $type = Model_Log_Message::MLM_LOCATION_LOG;

    /**
     * Creates a message that a ruin has been found
     * @param Model_Places_Abstract_Place $ruin Short message title
     * @param int $uid
     */
	public function __construct($ruin, $uid = null) {
        if ($uid === null) $uid = Globals::CurrentPlayer()->id();

        parent::__construct([
            'name' => Globals::CurrentGame()->get_player($uid)->name(),
            'ruin' => $ruin->name(),
            'icon' => $ruin->icon()
        ], $uid);
	}

    protected function postprocess($data) {
        $data['ruin'] = __($data['ruin']);
        return $data;
    }
}