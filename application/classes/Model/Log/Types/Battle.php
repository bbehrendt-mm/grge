<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle extends Model_Log_Message {

    protected static $type = Model_Log_Message::MLM_COMBAT;

	public function __construct($title, $text, $video_id, $summary) {
        parent::__construct([
            'bid' => $video_id,
            'msg' => $title,
            'bdy' => $text,
            'sum' => $summary
        ]);
	}

    protected function postprocess($data) {
        $data['msg'] = is_array($data['msg']) ? __($data['msg'][0], $data['msg'][1]) : __($data['msg']);
        if (isset($data['bdy']) && $data['bdy'])
            $data['bdy'] = is_array($data['bdy']) ? __($data['bdy'][0], $data['bdy'][1]) : __($data['bdy']);

        else unset($data['bdy']);

        foreach ($data['sum'] as &$group) {
            foreach ($group as &$entry) {
                if (isset($entry['name']) && !empty($entry['unique']))
                    $entry['name'] = __($entry['name']);

                if (isset($entry['injuries'])) {
                    foreach ($entry['injuries'] as &$inj) $inj[1] = _($inj[1]);
                    unset($inj);
                }

                if (isset($entry['damaged_items'])) {
                    foreach ($entry['damaged_items'] as &$itm) $itm[1] = _($itm[1]);
                    unset($itm);
                }
            }
            unset($group,$entry);
        }


        $data['gallery'] = ($gid = Model_Combat_Handler::in_gallery($data['bid'], Globals::PrimaryPlayerF()->id())) ? [
            'id' => $gid,
            'p' => Globals::PrimaryPlayerF()->id()
        ] : null;

        return $data;
    }
}