<?php defined('SYSPATH') or die('No direct script access.');

/** @noinspection LowerAccessLevelInspection */
abstract class Controller extends Kohana_Controller {

    /**
     * @var Session $session
     */
    protected $session;
    protected static $initialize_session = true;
    protected static $force_ajax = true;
    protected static $force_login = false;
    protected static $menu;
    private $widgets = array();
    private $notifications = array();
    private $data = array();
    private static $dumps = [];

    protected static $allow_etag_cache = false;

    protected static function post($key) {
        return Request::initial()->post($key);
    }

    /**
     * Returns true when the current request was made using AJAX calls
     * @return bool
     */
    protected function is_ajax_request(): bool {
        return ($this->request->headers('X-Requested-With') === 'XMLHttpRequest');
    }

    /**
     * This function will cause the script to abort when it was not called using AJAX
     */
    protected function force_ajax(): void {
        if (!$this->is_ajax_request()) {
            if ($this->request->action() === 'japi')
                die(GRGEError::m(\grge\E_HTTP_AJAX_REQUIRED));
            $this->response->body(View::factory('redirect')->set('url',URL::base())->set('path',$this->request->uri())->set('sid', $this->session->id()));
            $this->request->action('noaction');
        }
    }

    /**
     * Binds the active user object globally. Returns true, when the object was successfully bound; otherwise false
     * @return bool
     */
    private function get_user_obj(): bool {
        if (!Globals::hasCurrentUser())
            Globals::setCurrentUser($this->session->get('user',NULL));
        return Globals::hasCurrentUser();
    }

    /**
     * This function will cause the script to abort when the user is not logged in
     */
    private function force_login(): void {
        // Get user object, check if it is valid
        if (!$this->get_user_obj() || !Globals::CurrentUserF()->valid()) {
            // If we don't have a user object, destroy the current session and unbind global registers (just to be sure)
            if ($this->request->is_initial()) {
                Session::instance()->destroy();
                Globals::resetCurrentUser();
            }

            // Spawn an error message; if we aren't called via AJAX just die, otherwise call dummy action
            if (!$this->is_ajax_request())
                // Output error message as string
                die(GRGEError::m(\grge\E_SERVER_INVALID_SESSION));
            else {
                // Create JSON error output, then stop the action from being executed by redirecting to noaction
                $this->error(\grge\E_SERVER_INVALID_SESSION);
                $this->request->action('noaction');
            }
        }
    }

    /**
     * Controller initialization
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function before() {
        //Init error class
        GRGEError::i();

        //Load session
        if (static::$initialize_session) {
            $vcsid = $this->request->is_initial() ? null : $this->request->headers('X-Virtual-Cookie');
            $this->session = Session::instance(null, $vcsid ?: null);

            // Virtual login
            if ($this->request->is_initial())
                $this->perform_virtual_login();

            if (!$this->is_ajax_request())
                // Preserve initial get/post parameters
                $this->session->set('request',array_merge($_SERVER,['CLIENT_REQUEST' => $_REQUEST]));

            //Check AJAX
            if (static::$force_ajax)
                $this->force_ajax();

            // Perform session checks
            if (static::$force_login)
                $this->force_login();            
        } else $this->session = null;
        


        //Cache control
        $this->response->headers(static::$allow_etag_cache ? '' : 'Cache-Control: no-store, must-revalidate');

        if (Tool_Events::maintenance() && !(strtolower($this->request->directory()) === 'admin' || in_array(strtolower($this->request->controller()),['web', 'landing']))) {
            $this->request->action('noaction');
            if ($this->is_ajax_request()) $this->error(\grge\E_SERVER_LIMITED_MAINTENANCE);
        }

        // Skin Check
        $current_skin = $_COOKIE['skin'] ?? null;
        $event_skin = Tool_Events::current_skin();
        if ($current_skin !== $event_skin && !isset($_COOKIE['skin_cst']) && $this->is_ajax_request()) {
            setcookie('skin', $event_skin, 0, URL::base());
            $this->request->action('noaction');
            $this->error(\grge\E_SERVER_INVALID_SESSION);
        }
    }


    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    public function after() {
        if (static::$allow_etag_cache) {
            $resource = $this->response->body();
            $etag = md5($resource);
            $not_modified = $this->request->headers('X-Skip-ETag') !== '1' && (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag);

            if ($not_modified) {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }

            header('ETag: ' . $etag);
        }
    }

    public static function dump($title, $object): void {
        self::$dumps[$title] = $object;
    }

    private function daily_login_bonus(): void {
        $last = (int)DB::select('dailylogin')->from('users')->where('uid','=',Globals::CurrentUserF()->uid())->execute()->get('dailylogin',0);
        $num = (int)DB::select('logincount')->from('users')->where('uid','=',Globals::CurrentUserF()->uid())->execute()->get('logincount',0);

        $nulldate = new DateTime(); $nulldate->setTimestamp( 0 );
        $today = (new DateTime())->diff( $nulldate )->days;

        if ($last === $today) return;
        elseif ($last === ($today - 1)) {
            DB::update('users')->set(['dailylogin' => $today, 'logincount' => $num+1])->where('uid','=',Globals::CurrentUserF()->uid())->execute();
            $lv = ceil($num/7);

            $n = 0;
            if ($lv <= 0) {

            } elseif ($lv === 1) {
                $n = 5;
                $this->add_note('daily-login',__('Du hast dich :days Tage in Folge eingeloggt. Als kleine Belohnung erhälst du dafür :num BrainCoins. Viel Vergnügen damit!', [':days' => $num+1,':num' => $n]),__('Täglicher Login'));
            } elseif ($lv === 2) {
                $n = 10;
                $this->add_note('daily-login',__('Du hast dich bereits :days Tage in Folge eingeloggt. Als Belohnung erhälst du dafür :num BrainCoins. Viel Vergnügen damit!', [':days' => $num+1,':num' => $n]),__('Täglicher Login'));
            } elseif ($lv <= 4) {
                $n = 15;
                $this->add_note('daily-login',__('Du hast dich mittlerweise :days Tage in Folge eingeloggt. Als Dankeschön erhälst du dafür :num BrainCoins. Viel Vergnügen damit!', [':days' => $num+1,':num' => $n]),__('Täglicher Login'));
            } elseif ($lv <= 12) {
                $n = 25;
                $this->add_note('daily-login',__('Wow, seit :days Tagen bist du täglich hier. Als Dankeschön für deine Treue erhälst du :num BrainCoins. Viel Vergnügen damit!', [':days' => $num+1,':num' => $n]),__('Täglicher Login'));
            } else {
                $n = 35;
                $this->add_note('daily-login',__('Seit nunmehr :days Tagen kommst du täglich vorbei - wirklich beeindruckend! Damit hast du dir :num BrainCoins redlich verdient. Viel Vergnügen damit!', [':days' => $num+1,':num' => $n]),__('Täglicher Login'));
            }

            Globals::CurrentUserF()->award_coins(Globals::CurrentUserF()->uid(),$n);
        } else {
            DB::update('users')->set(['dailylogin' => $today, 'logincount' => 0])->where('uid','=',Globals::CurrentUserF()->uid())->execute();
            if ($num > 1)
                $this->add_note('daily-login-fail',__('Du hast dich seit :mdays Tagen nicht mehr eingeloggt. Das bedeutet leider, dass dein seit :days Tagen laufender Login-Bonus abgebrochen wird...', [':mdays' => $today - $last, ':days' => $num]),__('Täglicher Login abgebrochen...'));
        }
    }

    public function perform_virtual_login(): void {
        if (!$this->get_user_obj()) return;
        $last_update = $this->session->get('last_virtual_login',0);

        if ($last_update < (time()-1)) {
            $this->session->set('last_virtual_login',time());
            $this->daily_login_bonus();
        }
    }

    /**
     * Dummy action; used as a replacement for the actual action when the before-method needs to cancel execution, but can't completely kill the script by dieing
     */
    public function action_noaction(): void {}

    public function action_maintenance(): void {
        if (!Tool_Events::maintenance())
            self::redirect(URL::site('landing/redirect',true));
        else {
            $this->add_widget(View::factory('pages/maintenance')->set('slot', Tool_Events::active_maintenance_period())->render());
            $this->render();
        }


    }

    /**
     * Hook for AJAX calls using JAPI
     */
    public function action_japi(): void {
        // Get action, build method name using the prefix "japi_"
        $action = $this->request->param('jaction');
        $method = "japi_{$action}";

        // Fail if we have no action
        if (!$action)
            $this->error(\grge\E_HTTP_REQUEST_INCOMPLETE);
        // Call method, or fail if method does not exists
        else if (!method_exists($this, $method))
            $this->error(\grge\E_HTTP_REQUEST_INVALID, ['uri' => $this->request->uri()]);
        else $this->$method();
    }

    /**
     * Add some default data to the output chain, i.e. menus
     */
    private function render_defaults(): void {
        $this->add_data('current_url', $this->request->uri(), true);
        if (static::$menu && !isset($this->widgets['main-menu']))
            $this->add_menu(static::$menu);
    }

    protected function add_menu($menu, $additional_data = []): void {
        $view = View::factory('menus/' . $menu)->set('url_wiki', Tool_Htmlout::get_external_link('wiki'));

        foreach ($additional_data as $key => $value)
            $view->set($key,$value);

        $this->add_widget('main-menu',$view->render());
    }

    protected function modify_current_url($url): void {
        $this->add_data('current_url', $url);
    }

    /**
     * Add a new content widget to the output chain
     * @param string $widget Widget name; if content parameter is missing, this will be used instead as context.
     * @param string|null $content Widget content. When missing, the first parameter is interpreted as widget content, using "content" as widget name
     */
    protected function add_widget($widget, $content = null): void {
        if ($content === null) $this->widgets['content'] = $widget;
        else $this->widgets[$widget] = $content;
    }

    /**
     * Adds a notification to the output chain
     * @param string $type Notification type; if this is the only argument, it will be interpreted as message content instead
     * @param string|null $content Notification content
     * @param string|bool $title Notification title (optional)
     */
    protected function add_note($type, $content = null, $title = false): void {
        if ($content === null) $this->notifications[] = array('type' => 'info', 'content' => $title, 'title' => false);
        else $this->notifications[] = array('type' => $type, 'content' => $content, 'title' => $title);
        $this->session->set('notifications',$this->notifications);
    }

    /**
     * Adds arbitrary data to the output chain
     * @param string|array $key Data key; if this is an array, each key/value-pair in that array will be added to the output chain. In that case, the other parameters are ignored.
     * @param mixed|null $data Data to add
     * @param bool $no_override If a key with the same name is already set, this controls weather the old key should be overwritten. The default is false (which means the old one will be overwritten)
     */
    protected function add_data($key, $data = null, $no_override = false): void {
        if (is_array($key))
            $this->data = array_merge_recursive($this->data, $key);
        elseif (!isset($this->data[$key])) $this->data[$key] = $data;
        elseif (!$no_override) $this->data[$key] = array_merge_recursive($this->data[$key], $data);
    }

    protected function is_silent(): bool {
        return (bool)Request::$current->post('silent');
    }

    /**
     * Renders the output chain as JSON data
     *
     * @param null|bool|array $obj Set null to invoke render_defaults(); set to an array to invoke add_data(); set to anything else to just render the existing chain without adding any more data. If the script version indicates this is a beta environment, profiling data is always added regardless of this parameter
     * @param bool            $skip_notifications
     *
     * @return bool Always returns true
     * @throws Kohana_Exception
     */
    protected function render($obj = null, $skip_notifications = false): bool {
        if ($this->is_silent()) return true;

        $this->response->headers('Content-Type', 'application/json');

        // Invoke render_defaults() or add_data() depending on the object parameter
        if ($obj === null) {
            $this->render_defaults();
        } elseif (is_array($obj)) $this->add_data($obj);

        // Get version data, append profiling information when this is not a stable version

        if (Kohana::$environment === Kohana::DEVELOPMENT) {
            global $compression;
            $version_data = Kohana::$config->load('build.version');
            $this->add_data('profiling', [
                'version' => "GRGE {$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['maintenance']}-{$version_data['stage']}-{$version_data['build']} ({$version_data['date']})",
                'path' => $this->request->controller() . ' / ' . ($this->request->action() === 'japi' ? ($this->request->param('jaction') . ' (japi)') : $this->request->action()),
                'memory' => number_format((memory_get_peak_usage() - KOHANA_START_MEMORY) / 1024, 2).'KB',
                'time' => number_format(microtime(TRUE) - KOHANA_START_TIME, 5).'s',
                'compression' => number_format(($compression[0] === $compression[1]) ? 1 : $compression[1]/$compression[0] ,3)
            ], true);
        }

        // Add other content in out data chain
        $this->add_data('content', $this->widgets, true);
        if (!$skip_notifications && static::$initialize_session) {
            $this->add_data('notifications', $this->session->get('notifications',[]), true);
            $this->session->delete('notifications');
        }

        if (self::$dumps && Kohana::$environment === Kohana::DEVELOPMENT)
            $this->add_data('var_dump', self::$dumps, true);

        // Render
        $return_string = json_encode($this->data, JSON_FORCE_OBJECT);
        if ($return_string === false) {
            $code =  json_last_error();

            switch ($code) {
                case JSON_ERROR_UTF8:
                    $fun = null;
                    $fun = function(string $domain, $data) use (&$fun) {
                        foreach ($data as $key => $entry)
                            if (is_array($entry)) $fun( empty($domain) ? $key : "$domain.$key", $entry );
                            else if (is_string($entry) && !mb_check_encoding($entry)) {
                                $failed_string = iconv("UTF-8","UTF-8//TRANSLIT",$entry);
                                $correct_string = iconv("UTF-8","UTF-8//IGNORE",$entry);
                                $len = strlen($correct_string);
                                for ($i = 0; $i < $len; $i++) if ($correct_string[$i] !== $entry[$i]) break;
                                $seq = $failed_string[$i];
                                $seq_before = substr($correct_string, max(0,$i-64), 64);
                                $seq_after  = substr($correct_string, $i, 64);
                                throw new RuntimeException("Data transfer failed. Malformed UTF8 character '$seq' detected in property \"" . (empty($domain) ? 'ROOT' : $domain) . "\" between '$seq_before' and '$seq_after'.");
                            }

                    };
                    $fun('JSON', $this->data);
                    throw new RuntimeException('Data transfer failed. Malformed UTF8 character detected.');
                    break;
                default: throw new RuntimeException("Data transfer failed. Encoding error $code: " . json_last_error_msg());
            }
        }

        $this->response->body($return_string);

        return true;
    }

    /**
     * Causes an error to be rendered. This function writes to the output buffer, but does NOT halt script execution
     * @param mixed $c Error Code
     * @param mixed|null $additional_data Optional additional data to add to the output
     * @return bool Always returns false
     */
    protected function error($c, $additional_data = null): bool {
        $this->response->headers('Content-Type', 'application/json');
        $tmp = array('error' => array(
            'code' => $c,
            'name' => GRGEError::r($c),
            'message' => GRGEError::d($c),
            'details' => $additional_data,
        ));

        if (self::$dumps) $tmp['var_dump'] = self::$dumps;

        $this->response->body(json_encode($tmp, JSON_FORCE_OBJECT));
        return false;
    }

    protected function not_found($custom_uri = null): bool {
        $this->add_widget(View::factory('pages/notfound')
            ->set('uri', $custom_uri ?? $this->request->uri())
            ->render()
        );
        return $this->render();
    }
}