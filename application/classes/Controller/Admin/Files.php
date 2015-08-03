<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Files extends Controller_Admin_Admin {

    protected static $force_ajax = false;
    protected static $auto_require = ['TRANSLATE_MOD'];

    private static $evio_version = 2;

    public function action_export_translations() {
        if (!$this->priv_allow_all(['TRANSLATE_MOD'])) die(Error::m(\grge\E_SERVER_ACCESS_DENIED));

        if (!($data = I18n::export())) die(Error::m(\grge\E_HTTP_REQUEST_POINTLESS));

        $data = gzcompress(serialize([
            'data' => $data,
            'meta' => [
                'format' => 'evio',
                'version' => static::$evio_version,
                'timestamp' => time(),
                'primary' => I18n::get_primary_language(),
                'languages' => I18n::get_languages()
            ]
        ]), 9);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . 'grge_evio_' . time() . '.evl"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . mb_strlen($data, '8bit'));

        echo $data;
    }

    private function legacy_grl_import($lang, $data) {
        if (!($orig = I18n::load($lang)) || !$data || !is_array($data)) die(Error::m(\grge\E_HTTP_REQUEST_POINTLESS));

        echo "GRGE LANGUAGE FILE IMPORTER<br />-----<br />Importing \"{$_FILES['grl']['name']}\" ({$_FILES['grl']['size']} bytes) into local package \"{$lang}\"<br />-----<br />";
        echo "Language Pack contains " . count($data) . " entries.<br />-----<br />";

        $added = 0;
        $ignored = 0;
        $changed = 0;
        $conflicts = 0;

        foreach ($data as $key => $translation) {
            if (!isset($orig[$key]))
                $added++;

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

            I18n::set($key, $translation, $lang);
            $changed++;
        }

        echo "-----<br />";
        echo ($added + $changed == 0) ? "No changes were made.<br />" : "Added $added new entries, updated $changed entries.<br />";
        echo "$ignored entries were ignored due to being identical with the local ones.<br />";
        if ($conflicts > 0) echo "!! $conflicts merge conflicts were detected. Please consult the log for more details and resolve them manually. !!<br />";

        echo "DONE";
    }

    public function action_import_translations() {
        if (!$this->priv_allow_all(['TRANSLATE_MOD'])) die(Error::m(\grge\E_SERVER_ACCESS_DENIED));

        if (!($file = file_get_contents($_FILES['grl']['tmp_name']))) die(Error::m(\grge\E_HTTP_REQUEST_INCOMPLETE));
        $data = unserialize(gzuncompress($file));

        if (!$data || !is_array($data)) die(Error::m(\grge\E_HTTP_REQUEST_POINTLESS));

        echo "GRGE EVIO LANGUAGE IMPORTER V" . static::$evio_version . "<br /><br />";

        if (!isset($data['data']) || !isset($data['meta']) || !is_array($data['meta'])) {
            if (!($lang = $this->request->post('lang'))) die(Error::m(\grge\E_HTTP_REQUEST_INCOMPLETE));

            echo "This seems to be a legacy GRL Package. Now switching to legacy import system ...<br /><br />";
            return $this->legacy_grl_import($lang, $data);
        }


        echo "Importing from \"{$_FILES['grl']['name']}\" ({$_FILES['grl']['size']} bytes)...<br />-----<br />";

        if (!isset($data['meta']['format']) || !isset($data['meta']['version']) || !isset($data['meta']['timestamp']) || !isset($data['meta']['primary']) || !isset($data['meta']['languages'])) {
            echo "ERROR: Package is missing header information. Aborting.";
            return false;
        }

        if ($data['meta']['format'] !== 'evio' || $data['meta']['version'] != 2) {
            echo "ERROR: Package format is unsupported ({$data['meta']['format']}/{$data['meta']['version']}). Aborting.";
            return false;
        }

        if ($data['meta']['primary'] !== I18n::get_primary_language()) {
            echo "ERROR: Package does not use the correct primary language. Aborting.";
            return false;
        }

        $primary = I18n::get_primary_language();
        $languages = I18n::get_languages();
        foreach ($data['meta']['languages'] as $l)
            if (!in_array($l, $languages))
                echo "WARNING: Package contains entries for unsupported language '$l'. These entries will be ignored.<br />";
        foreach ($languages as $l)
            if (!in_array($l, $data['meta']['languages']))
                echo "WARNING: Package is missing entries for language '$l'.<br />";

        echo "Package information: " . count($data) . " entries, EVIO version {$data['meta']['version']}, created " . date('c', $data['meta']['timestamp']) . "<br />-----<br />";

        $compare = I18n::export();

        $conflicts = [];

        foreach ($data as $hash => $line) {

            if (!isset($line[$primary]))
                continue;

            $key = null;
            if (!isset($compare[$hash])) {
                $key = $line[$primary];
                if (!I18n::set_missing($key)) {
                    echo "ERROR: Unable to create entry "  . bin2hex($hash) . ". Aborting.";
                    return false;
                };
                $compare[$hash] = [$primary => $key];
                echo "Created entry: " . bin2hex($hash) . "<br />";
            } else $key = $compare[$hash][$primary];

            foreach ($languages as $lang) {

                if (!isset($line[$lang])) continue;

                $original = isset($compare[$hash][$lang]) ? $compare[$hash][$lang] : null;

                if ($original === null) {
                    if (!I18N::set($key, $line[$lang], $lang)) {
                        echo "ERROR: Unable to add a translation for '$lang' to " . bin2hex($hash) . ". Aborting.";
                        return false;
                    }
                    echo "Added translation for '$lang' to "  . bin2hex($hash) . "<br />";
                } elseif ($original !== $line[$lang]) {
                    $conflicts[] = [bin2hex($hash), $key, $original, $line[$lang]];
                    echo "WARNING: Conflict detected in "  . bin2hex($hash) . ". '$lang' contains a different translation.";
                }
            }
        }

        echo "-----<br />";

        if ($conflicts) {
            echo "<b>Conflict details below:</b> <br />";
            echo "<table cellpadding=\"5px\"><thead><tr><td>Key</td><td>Text</td><td>Local Translation</td><td>Package Translation</td></tr></thead>";
            foreach ($conflicts as list($hash, $key, $original, $conflict)) {
                echo "<tr><td>$hash</td><td>$key</td><td>$original</td><td>$conflict</td></tr>";
            }
            echo "</table>";
        }

        echo "<br /><br /><b>DONE</b>";
        return true;
    }
}