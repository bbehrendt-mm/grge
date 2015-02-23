<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Files extends Controller_Admin_Admin {

    protected static $force_ajax = false;
    protected static $auto_require = ['TRANSLATE_MOD'];

    public function action_export_translations() {
        if (!$this->priv_allow_all(['TRANSLATE_MOD'])) die(Error::m(\grge\E_SERVER_ACCESS_DENIED));

        if (!($lang = $this->request->param('id'))) die(Error::m(\grge\E_HTTP_REQUEST_INCOMPLETE));

        if (!($data = I18n::get_all($lang))) die(Error::m(\grge\E_HTTP_REQUEST_POINTLESS));

        $data = gzcompress(serialize($data), 9);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . 'grge_langfile_' . $lang . '.grl"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . mb_strlen($data, '8bit'));

        echo $data;
    }

    public function action_import_translations() {
        if (!$this->priv_allow_all(['TRANSLATE_MOD'])) die(Error::m(\grge\E_SERVER_ACCESS_DENIED));

        if (!($lang = $this->request->post('lang')) || !($file = file_get_contents($_FILES['grl']['tmp_name']))) die(Error::m(\grge\E_HTTP_REQUEST_INCOMPLETE));
        $data = unserialize(gzuncompress($file));
        if (!($orig = I18n::get_all($lang)) || !$data || !is_array($data)) die(Error::m(\grge\E_HTTP_REQUEST_POINTLESS));

        echo "GRGE LANGUAGE FILE IMPORTER<br />-----<br />Importing \"{$_FILES['grl']['name']}\" ({$_FILES['grl']['size']} bytes) into local package \"{$lang}\"<br />-----<br />";
        echo "Language Pack contains " . count($data) . " entries.<br />-----<br />";

        $added = 0;
        $ignored = 0;
        $changed = 0;
        $conflicts = 0;

        $ss = []; $tt = [];
        foreach ($data as $key => $translation) {
            if (!isset($orig[$key])) $added++;

            $original = __($key, -1, $lang);
            if ($translation === $original) {
                $ignored++;
                continue;
            }
            if ($original != $key) {
                echo "CONFLICT DETECTED!<br />Key<pre>{$key}</pre>Local Translation<pre>{$original}</pre>Remote Translation<pre>{$translation}</pre><br /><br />";
                $conflicts++;
                continue;
            }

            $ss[] = $key; $tt[] = $translation;
            $changed++;
        }

        echo "-----<br />";
        echo ($added + $changed == 0) ? "No changes were made.<br />" : "Added $added new entries, updated $changed entries.<br />";
        echo "$ignored entries were ignored due to being identical with the local ones.<br />";
        if ($conflicts > 0) echo "!! $conflicts merge conflicts were detected. Please consult the log for more details and resolve them manually. !!<br />";

        I18n::write();
        I18n::set($ss, $tt, $lang);
        I18n::set_readonly_flag();
        echo "DONE";
    }
}