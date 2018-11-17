<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Postbox {

    protected $messages = [];
    protected $beacon = 0;

    /**
     * Receive a message
     *
     * @param int    $uid User id
     * @param string $message
     * @param string $title
     *
     * @throws Exception
     */
    public function add($uid, $message, $title) {
        $id = time() . random_int(0,99);
        $this->messages[$id] = array('uid' => $uid, 'message' => $message, 'title' => $title, 'timestamp' => time(), 'read' => false, 'mid' => $id);
        while (count($this->messages) > 50) {
            $d = array_keys($this->messages);
            $this->remove($d[0]);
        }
    }

    /**
     * Delete a message
     * @param $id
     */
    public function remove($id) {

        unset($this->messages[$id]);
    }

    /**
     * Mark message as read
     * @param $id
     */
    public function read($id) {
        if (isset($this->messages[$id])) $this->messages[$id]['read'] = true;
    }

    /**
     * Returns all messages
     * @param bool $reverse
     * @param bool $filter_read
     * @return array
     */
    public function get($reverse = true, $filter_read = false) {
        if ($filter_read) {
            $ret = array();
            foreach ($this->messages as $msg)
                if (!$msg['read'])
                    $ret[] = $msg;
        } else $ret = $this->messages;

        return $reverse ? array_reverse($ret) : $ret;
    }

    /**
     * Activates the chat beacon for a given amount of time, or returns the beacon status
     * @param null $minutes Null to return beacon state; negative number to reset beacon state; positive number to activate beacon
     * @return bool|number
     */
    public function beacon($minutes = null) {
        if ($minutes === null) return ($this->beacon >= time());
        elseif ($minutes < 0) return $this->beacon = 0;
        else return $this->beacon = time() + $minutes * 60;
    }
}