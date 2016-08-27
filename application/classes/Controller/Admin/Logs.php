<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Logs extends Controller_Admin_Admin {

    protected static $auto_require = ['LOGVIEW'];

    private function accumulate_logs() {
        $path = APPPATH . 'logs';
        $list = [];

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach($files as $name => $file) {
            $filename = $file->getFilename();
            if ($filename[0] == '.' || substr($filename, -4) !== '.php') continue;

            $list[] = str_replace(['\\','/'],'-',substr($file->getPath(), strlen($path) + 1) . '\\' . substr($filename, 0, -4));
        }

        ksort($list);
        return $list;
    }

    public function japi_fetch() {
        $id = $this->post('id');
        $data = explode('-',$id);
        if (count($data) != 3) return $this->render(['id' => null]);

        $file = APPPATH . 'logs/' . $data[0] . '/' . $data[1] . '/' . $data[2] . '.php';
        if (!file_exists($file))
            return $this->render(['id' => null]);

        $lines = file($file);
        $ret = array();
        $c1 = $c2 = null;
        foreach ($lines as $line) {
            if (!preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) -{3} (\w*): (.*)/', $line, $matches)) {
                if (!$c1 || !$c2) continue;
                if (!isset($ret[$c1]) || !isset($ret[$c1][$c2])) continue;
            } else {
                list(,$c1, $c2, $line) = $matches;
                if (!isset($ret[$c1])) $ret[$c1] = array($c2 => array());
                if (!isset($ret[$c1][$c2])) $ret[$c1][$c2] = array();
            }
            $ret[$c1][$c2][] = $line;
        }

        return $this->render([
            'id' => $id,
            'log' => $ret,
        ]);
    }


    public function action_main() {


        $this->add_widget(View::factory('admin/logs')
            ->set('dates', $this->accumulate_logs())
            ->render());

        $this->render();
    }
}