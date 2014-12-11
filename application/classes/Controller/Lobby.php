<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Lobby extends Controller {

    protected static $force_login = true;

    public function action_main() {
        /**
         * @global Model_EUser $user
         */
        global $user;
        $this->add_widget('main-menu',View::factory('menus/logout')->render());
        $this->add_widget(View::factory('pages/main')
            ->set('ingame', (bool)$user->get_current_game())
            ->render());
        $this->render();
    }

    public function japi_feedproxy() {
        $offset = max(0,(int)$this->request->current()->post('page') - 1) * 5;
        $url = Kohana::$config->load('services.newsfeed.server');
        $auth = Kohana::$config->load('services.newsfeed.token');
        $fid = Kohana::$config->load('services.newsfeed.topics');

        if (isset($fid[I18n::$lang])) $fid = $fid[I18n::$lang];
        else $fid = $fid['default'];

        try {
            $ret = json_decode(file_get_contents("{$url}/remote.php", false, stream_context_create([
                'http' => array(
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
                throw new Exception('Failed to retrieve forum listing.');
        } catch (Exception $e) {
            return $this->error(\grge\E_EXT_SERVICE_UNAVAILABLE);
        }

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