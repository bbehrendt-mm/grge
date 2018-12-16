<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Translate extends Controller_Admin_Admin {

    protected static $auto_require = ['TRANSLATE'];

    private function loader($url): void
    {
        $this->add_widget(View::factory($url)
            ->set('base', 'de')
            ->set('langs', ['en','es','fr','ru'])
            ->set('adv_priv', static::priv_allow_all('TRANSLATE_MOD'))
            ->render());

        $this->render();
    }

    public function action_main(): void
    {
        $this->loader('admin/translate2');
    }

    public function action_old(): void
    {
        $this->loader('admin/translate');
    }

    public function japi_del(): bool
    {
        if (!static::priv_allow_all('TRANSLATE_MOD'))
            return $this->error(\grge\E_SERVER_ACCESS_DENIED);

        $this->render(['success' => (int)I18n::remove(self::post('id'))]);
        return true;
    }

    public function japi_next(): bool
    {

        $id = (int)self::post('id');
        $tr = self::post('translation');
        $from = self::post('from');
        $to = self::post('to');
        $rq_id = (int)self::post('request');

        $b = true;
        if ($to && $id && trim($tr))
            $this->add_data('success', $b = (bool)I18n::set_by_id($id, $tr, $to));

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

    public function japi_search(): bool
    {
        $q = trim(self::post('q'));
        if (strlen($q) < 4) return $this->render(['result' => []]);

        return $this->render(['result' => array_map(function($id) {
            $entry = I18n::get_by_id($id);
            return [
                'id' => $id,
                'from' => $entry[$this->post('from')],
                'to'  => $entry[$this->post('to')]
            ];
        }, I18n::search($q, [self::post('from'), self::post('to')]))]);
    }

    public function japi_add(): bool
    {
        if (!static::priv_allow_all('TRANSLATE_MOD'))
            return $this->error(\grge\E_SERVER_ACCESS_DENIED);

        $this->render(['success' => (int)I18n::set_missing(trim(self::post('q')))]);
        return true;
    }

    public function japi_set(): void
    {
        $this->render(['success' => (int)I18n::set(
                self::post('from'),
                self::post('to'),
                self::post('language')
        )]);
    }

    private function get_missing($lang, $mask): bool
    {
        $tmp = [];
        $tmp_m = [];
        foreach (I18n::get_missing($lang) as $k) {
            $tmp[$k] = $k;
            if ($mask !== 'de')
                $tmp_m[$k] = __($k, -1, $mask);
        }
        if ($mask === 'de')
            $tmp_m = [];

        return $this->render([
            'translations' => $tmp,
            'masks' => $tmp_m,
        ]);
    }

    private function get_auto($lang, $mask): bool
    {
        $tmp = [];
        $tmp_m = [];
        foreach (I18n::load($lang) as $s => $t)
            if ($s === $t)
                $tmp[$s] = $t;
        if ($mask === 'de') $tmp_m = [];
        else foreach ($tmp as $k)
            $tmp_m[$k] = __($k, -1, $mask);

        return $this->render([
            'translations' => $tmp,
            'masks' => $tmp_m,
        ]);
    }

    private function get_all($lang, $mask, $search): bool
    {
        $tmp = [];
        $tmp_m = [];
        foreach (I18n::load($lang) as $s => $t)
            if (!$search || strpos($s, $search) !== false || strpos($t, $search) !== false || ($mask
                    !== 'de' && strpos(__($s, -1, $mask), $search) !== false))
                $tmp[$s] = $t;
        if ($mask === 'de') $tmp_m = [];
        else foreach ($tmp as $k => $v)
            $tmp_m[$k] = __($k, -1, $mask);

        return $this->render([
            'translations' => $tmp,
            'masks' => $tmp_m,
        ]);
    }

    public function japi_get(): bool {
        $lang = self::post('language');
        $mask = self::post('mask');
        $source = self::post('source');
        $search = self::post('search');

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