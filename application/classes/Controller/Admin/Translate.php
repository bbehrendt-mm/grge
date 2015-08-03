<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Translate extends Controller_Admin_Admin {

    protected static $auto_require = ['TRANSLATE'];

    public function action_main() {
        $this->add_widget(View::factory('admin/translate')
            ->set('base', 'de')
            ->set('langs', ['en','es'])
            ->set('adv_priv', static::priv_allow_all('TRANSLATE_MOD'))
            ->render());

        $this->render();
    }

    public function japi_del() {
        if (!static::priv_allow_all('TRANSLATE_MOD'))
            return $this->error(\grge\E_SERVER_ACCESS_DENIED);

        $this->render(['success' => (int)I18n::remove($this->request->post('from'))]);
        return true;
    }

    public function japi_set() {
        $this->render(['success' => (int)I18n::set(
                $this->request->post('from'),
                $this->request->post('to'),
                $this->request->post('language')
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
        $lang = $this->request->post('language');
        $mask = $this->request->post('mask');
        $source = $this->request->post('source');
        $search = $this->request->post('search');

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