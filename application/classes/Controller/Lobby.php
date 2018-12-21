<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Lobby extends Controller {

    protected static $force_login = true;

    /**
     * News Renderer
     */
    public function action_main(): void
    {
        $this->add_menu('logout');
        $this->add_widget(View::factory('pages/main')
            ->set('ingame', (bool)Globals::CurrentUserF()->get_current_game())
            ->set('avatar', Model_Euser::avatar_by_id(Globals::CurrentUserF()->uid()))
            ->set('name', Globals::CurrentUserF()->name())
            ->set('mentor', Model_Euser::get_mentoring_ref(Globals::CurrentUserF()->uid()))
            ->set('cashout', Model_Euser::get_mentor_braincoins(Globals::CurrentUserF()->uid(), null, false))
            ->set('pupils', count(Globals::CurrentUserF()->get_apprentice_id()))
            ->set('bc', Model_Euser::get_coins(Globals::CurrentUserF()->uid()))
            ->render());
        $this->render();
    }

    /**
     * New user landing page
     */
    public function action_newuser(): bool
    {
        if (Globals::CurrentUserF()->soulpoints() > 0 || Model_Euser::mentor_id(Globals::CurrentUserF()->uid()) !== null) {
            self::redirect(URL::site('lobby/main',true));
            return false;
        }

        $this->add_menu('logout');
        $this->add_widget(View::factory('pages/newuser')
            ->set('name', Globals::CurrentUserF()->name())
            ->render());
        return $this->render();
    }

    private function get_feed($fid, $length, $offset) {

        $cacheable = ($length === 5 && $offset === 0);
        $cache = $cacheable ? Cache::instance()->get("forum_default_$fid", null) : null;
        if (is_string($cache)) $cache = unserialize($cache, ['allowed_classes' => false]);
        $cached = !(!$cache || !$cache['time'] || !$cache['data']);

        if (!$cached || $cache['time'] < (time() - 300)) {

            $url = Kohana::$config->load('services.newsfeed.server');
            $auth = Kohana::$config->load('services.newsfeed.token');

            // Connect to forum, read threads
            try {
                $ret = json_decode(file_get_contents("{$url}/remote.php", false, stream_context_create([
                    'http' => array(
                        'timeout' => $cached ? 5 : 10,
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query([
                            'auth' => $auth,
                            'f' => $fid,
                            's' => 5,
                            'o' => $offset
                        ]),
                    ),
                ])), true);

                if ($ret === null)
                    throw new RuntimeException('Failed to retrieve forum listing.');
                else {
                    if ($cacheable) Cache::instance()->set("forum_default_$fid", serialize(['time' => time(), 'data' => $ret]));
                    return $ret;
                }
            } catch (Exception $e) {
                return $cached ? $cache['data'] : false;
            }

        } else return $cache['data'];

    }

    /**
     * Forum Newsfeed API
     * @return bool
     * @throws Kohana_Exception
     */
    public function japi_feedproxy(): bool
    {
        // Get config
        $offset = max(0,(int)Request::current()->post('page') - 1) * 5;
        $url = Kohana::$config->load('services.newsfeed.link');
        $fid = Kohana::$config->load('services.newsfeed.topics');

        // Get forum ID based on language, or use default if no specific ID is set
        if (isset($fid[I18n::$lang])) $fid = $fid[I18n::$lang];
        else $fid = $fid['default'];

        $ret = $this->get_feed($fid, 5, $offset);
        if (!$ret) return $this->error(\grge\E_EXT_SERVICE_UNAVAILABLE);

        // Convert stuff
        foreach ($ret['threads'] as &$article) {
            $article['date'] = date(__('G:i \U\h\r \a\m d.m.'), $article['date']);
            $article['response'] = "{$url}/posting.php?mode=reply&f={$fid}&t={$article['id']}";
            $article['view'] = "{$url}/viewtopic.php?f={$fid}&t={$article['id']}";
            $article['content']['text'] = str_replace('{SMILIES_PATH}', "{$url}/{$ret['smileys']}", $article['content']['text']);
        }

        return $this->render([
            'feeds' => $ret['threads']
        ]);
    }
}