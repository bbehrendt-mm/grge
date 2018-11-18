<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Auth_Legacy extends Model_Auth_Interface {

    protected static $host;

    private $sk;
    private $name = null;
    private $avatar = null;

    public static function get_service_name() {
        return static::$host;
    }

    public function __construct($secret_key, $allow_fallback = true) {
        if (is_string($tmp_error = $this->login_remote($secret_key))) {
            $this->last_error = $tmp_error;
            if ($allow_fallback) $this->login_local($secret_key);
        }
    }

    public function connectToLocal($target_id = null): bool
    {
        if ($this->rid < 0) return false;
        if ($this->zvid > 0 && !$target_id) return true;

        $this->zvid = $target_id ?: static::lookup($this->rid);

        $created = false;

        if ($this->zvid <= 0) {
            $created = true;
            $this->zvid = Model_Euser::register($this->name,$this->avatar);
        }
        elseif ($this->avatar && !Model_Euser::avatar_by_id($this->zvid)) Model_Euser::user_update_avatar($this->zvid, $this->avatar);

        $this->ready = true;
        $this->link($this->sk);
        return $created ? 2 : true;
    }

    public static function fetch_player_xml($pid, $city = true) {
        $k = static::user_get_values($pid);
        return (!$k || !$k[0]) ? null : static::fetch_xml($k[0]);
    }

    private static function fetch_xml($k, $city = true) {
        // Check host and build URL
        if (!Kohana::$config->load('mt.links.' . static::$host))
            return null;
        $sk = Kohana::$config->load('mt.links.' . static::$host . '.token');
        $url = 'http://' . Kohana::$config->load('mt.links.' . static::$host . '.url') . '/xml/'
            . ($city ? '' : 'ghost') ."?k={$k};sk={$sk}";

        // Get XML
        $context = stream_context_create(['http'=> ['timeout' => 10]]);
        $xml = new DOMDocument( );
        try {
            if (!$xml->loadXML(mb_convert_encoding(file_get_contents($url, false, $context),
                'UTF-8', 'UTF-8'
            )))
                return null;
            return $xml;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getLegacyTownInfo($pid): array {
        if ($xml = self::fetch_player_xml($pid)) {

            $xpath = new DOMXPath($xml);
            $error = $xpath->evaluate('string(//error/@code)');
            if ($error === 'not_in_game')
                return [-1,null,null];
            elseif ($error) return [-2,null,null];

            $id = (int)$xpath->evaluate('string(//game/@id)');
            $name = $xpath->evaluate('string(//city/@city)');
            $job = $xpath->evaluate('string(//owner/citizen/@job)');

            return [$id,$name,$job];
        } else return [null,null,null];
    }

    private function login_remote($secret_key) {
        $xml = static::fetch_xml($secret_key);

        //Create xpath selector
        $xpath = new DOMXPath($xml);

        //Let's check for errors first
        if ($error = $xpath->evaluate('string(//error/@code)')) if ($error
            !== 'not_in_game')
            return $error;

        $this->rid = (int)$xpath->evaluate('string(//owner/citizen/@id)');
        $this->name = $xpath->evaluate('string(//owner/citizen/@name)');
        $this->avatar = $xpath->evaluate('string(//owner/citizen/@avatar)');

        if (!$this->rid || !$this->name)
            return 'supplement_failed';

        $this->sk = $secret_key;
        return true;
    }

    private function login_local($secret_key): void
    {
        $this->zvid = static::lookup(null, $secret_key);

        if ($this->zvid >= 0) {
            $this->rid = (int)DB::select('rid')->from('profiles_xref')->where('provider','=', static::class)->where('zvid','=',$this->zvid)->execute()->get('rid',-1);
            $this->name = Model_Euser::name_by_id($this->zvid);
            $this->avatar = Model_Euser::avatar_by_id($this->zvid);

            $this->ready = true;
        }
    }

    public static function retrieve_user_id($key): int
    {
        return (int)DB::select('zvid')->from('profiles_xref')->where('provider','=', static::class)->where('var1','=',$key)->execute()->get('zvid',-1);
    }

    public function getRemoteName() {
        return $this->is_ready() ? $this->name : null;
    }

    public function getRemoteAvatarUrl() {
        return $this->is_ready() ? $this->avatar : null;
    }

}