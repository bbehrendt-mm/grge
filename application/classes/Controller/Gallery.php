<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Gallery extends Controller {

    protected static $force_ajax = true;

    public function japi_rename(): bool
    {
        $id = (int)self::post('id');
        $label = trim(self::post('label'));

        $entry = Model_Combat_Handler::gallery_by_id($id);
        if (!$entry || !$label) return $this->render(['success' => 0]);
        else $entry = array_shift($entry);

        if ($entry['pid'] !== Globals::CurrentUserF()->uid()) return $this->render(['success' => 0]);

        Model_Combat_Handler::add_to_gallery($entry['bid'], Globals::CurrentUserF()->uid(), $label);

        return $this->render(['success' => 1]);
    }

    public function japi_delete(): bool
    {
        $id = (int)self::post('id');

        $entry = Model_Combat_Handler::gallery_by_id($id);
        if (!$entry) return $this->render(['success' => 0]);
        else $entry = array_shift($entry);

        if ($entry['pid'] !== Globals::CurrentUserF()->uid()) return $this->render(['success' => 0]);

        Model_Combat_Handler::delete_from_gallery($id);

        return $this->render(['success' => 1]);
    }
}