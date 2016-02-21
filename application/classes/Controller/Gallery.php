<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Gallery extends Controller {

    protected static $force_ajax = true;

    public function japi_rename() {
        /** @global Model_Euser $user */
        global $user;

        $id = (int)$this->post('id');
        $label = trim($this->post('label'));

        $entry = Model_Combat_Handler::gallery_by_id($id);
        if (!$entry || !$label) return $this->render(['success' => 0]);
        else $entry = array_shift($entry);

        if ($entry['pid'] != $user->uid()) return $this->render(['success' => 0]);

        Model_Combat_Handler::add_to_gallery($entry['bid'], $user->uid(), $label);

        return $this->render(['success' => 1]);
    }

    public function japi_delete() {
        /** @global Model_Euser $user */
        global $user;

        $id = (int)$this->post('id');

        $entry = Model_Combat_Handler::gallery_by_id($id);
        if (!$entry) return $this->render(['success' => 0]);
        else $entry = array_shift($entry);

        if ($entry['pid'] != $user->uid()) return $this->render(['success' => 0]);

        Model_Combat_Handler::delete_from_gallery($id);

        return $this->render(['success' => 1]);
    }
}