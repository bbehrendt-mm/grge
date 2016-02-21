<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Translate extends Controller_Admin_Admin {

    protected static $auto_require = ['TRANSLATE'];

    private function loader($url) {
        $this->add_widget(View::factory($url)
            ->set('base', 'de')
            ->set('langs', ['en','es','fr'])
            ->set('adv_priv', static::priv_allow_all('TRANSLATE_MOD'))
            ->render());

        $this->render();
    }

    public function action_main() {
        $this->loader('admin/translate2');
    }

    public function action_old() {
        $this->loader('admin/translate');
    }

    public function japi_del() {
        if (!static::priv_allow_all('TRANSLATE_MOD'))
            return $this->error(\grge\E_SERVER_ACCESS_DENIED);

        $this->render(['success' => (int)I18n::remove($this->post('from'))]);
        return true;
    }

    public function japi_next() {

        $id = (int)$this->post('id');
        $tr = $this->post('translation');
        $from = $this->post('from');
        $to = $this->post('to');
        $rq_id = (int)$this->post('request');

        $b = true;
        if (trim($tr) && $id && $to) {
            $this->add_data('success', $b = (bool)I18n::set_by_id($id, $tr, $to));
        }

        if (!$b) return $this->render();
        else I18n::unlock($id);

        if (!$rq_id) {
            $rq_id = I18n::get_next_missing($to, time() - 300, $id);
            if (!$rq_id) $rq_id = I18n::get_next_missing($to, time() - 60);
        }
        $next = I18n::get_by_id($rq_id);

        if ($next) {
            I18n::lock($rq_id);
            $this->add_data('next', [
                'id' => $rq_id,
                'original' => $next[$from],
                'translation' => $next[$to]
            ]);
        }

        $this->add_data('completion', I18n::completion($to));
        return $this->render();

    }

    public function japi_set() {
        $this->render(['success' => (int)I18n::set(
                $this->post('from'),
                $this->post('to'),
                $this->post('language')
        )]);
    }

    private function get_missing($lang, $mask) {
        $tmp = [];
        $tmp_m = [];
        foreach (I18n::get_missing($lang) as $k) {
            $tmp[$k] = $k;
            if ($mask != 'de')
                $tmp_m[$k] = __($k, -1, $mask);
        }
        if ($mask == 'de')
            $tmp_m = [];

        return $this->render([
            'translations' => $tmp,
            'masks' => $tmp_m,
        ]);
    }

    private function get_auto($lang, $mask) {
        $tmp = [];
        $tmp_m = [];
        foreach (I18n::load($lang) as $s => $t)
            if ($s == $t)
                $tmp[$s] = $t;
        if ($mask == 'de') $tmp_m = [];
        else foreach ($tmp as $k)
            $tmp_m[$k] = __($k, -1, $mask);

        return $this->render([
            'translations' => $tmp,
            'masks' => $tmp_m,
        ]);
    }

    private function get_all($lang, $mask, $search) {
        $tmp = [];
        $tmp_m = [];
        foreach (I18n::load($lang) as $s => $t)
            if (!$search || strpos($s, $search) !== false || strpos($t, $search) !== false || ($mask != 'de' && strpos(__($s, -1, $mask), $search) !== false))
                $tmp[$s] = $t;
        if ($mask == 'de') $tmp_m = [];
        else foreach ($tmp as $k => $v)
            $tmp_m[$k] = __($k, -1, $mask);

        return $this->render([
            'translations' => $tmp,
            'masks' => $tmp_m,
        ]);
    }

    public function japi_get() {
        $lang = $this->post('language');
        $mask = $this->post('mask');
        $source = $this->post('source');
        $search = $this->post('search');

        switch ($source) {
            case 'prefetch': return $this->get_missing($lang,$mask);
            case 'auto': return $this->get_auto($lang,$mask);
            case 'all': return $this->get_all($lang,$mask,$search);
            default: return $this->render([
                'translations' => [],
                'masks' => [],
            ]);
        }
    }
}