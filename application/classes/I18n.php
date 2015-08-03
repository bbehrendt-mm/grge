<?php 

if ( !function_exists('__'))
{
    /**
     * Kohana translation/internationalization function. The PHP function
     * [strtr](http://php.net/strtr) is used for replacing parameters.
     *
     *    __('Welcome back, :user', array(':user' => $username));
     *
     * [!!] The target language is defined by [I18n::$lang].
     *
     * @uses    I18n::get
     * @param string $string
     * @param array|int $values values to replace in the translated text - -1 to disable default replacements
     * @param string $lang source language
     * @return  string
     */
	function __($string, $values = null, $lang = null)
	{
        $values = ($values === -1) ? [] : array_merge(empty($values) ? [] : $values, [
            //Defaults
            '::i::' => '<i>',
            '::/i::' => '</i>',
            '::b::' => '<b>',
            '::/b::' => '</b>'
        ]);

        $lang = explode('-',$lang)[0];
		return empty($values) ? I18n::get($string, $lang) : strtr(I18n::get($string, $lang), $values);
	}
}

if ( !function_exists('__j'))
{
    function __j($string, array $values = null, $lang = null)
    {
        return json_encode(__($string, $values, $lang));
    }
}


class I18n extends Kohana_I18n {
	// Cache of missing strings
	protected static $cache = array();
    protected static $readonly = false;

	protected static $lang_list = array('de', 'en', 'es');

    public static function get_primary_language() {
        return static::$lang_list[0];
    }

    public static function get_languages($include_primary = false) {
        return $include_primary ? static::$lang_list : array_slice(static::$lang_list, 1);
    }
    
    /**
     * Returns a complete translation table
     * @param string|null $lang Language, or null to use default
     * @return array
     */
    public static function load($lang = NULL) {
        if (!$lang) $lang = static::$lang;
        if (!in_array($lang, static::$lang_list)) return [];
        if (isset(static::$_cache[$lang])) return static::$_cache[$lang];

        $q = $lang == static::get_primary_language() ? DB::select($lang) : DB::select(static::get_primary_language(), $lang);
        return static::$_cache[$lang] = $q->from('language')->execute()->as_array(static::get_primary_language(), $lang);
    }

    public static function export() {
        return array_map(function($a) {unset($a['hash']); return $a;}, DB::select(array_merge(['hash'],static::$lang_list))->from('language')->execute()->as_array('hash'));
    }

    /**
     * Prevents changes to the language files to be written to disc.
     */
    public static function set_readonly_flag() {
        static::$readonly = true;
    }

    protected static function flush_cache() {
        static::$_cache = [];
    }

    /**
     * Changes an existing translation. This function can NOT add a completely new original/translation pair
     * @param string $string Original string
     * @param string $translation Translated string
     * @param string $lang Translation language
     * @return bool True when successfull, otherwise false
     */
    public static function set($string, $translation, $lang) {
        if (static::$readonly) return true;
        static::flush_cache();

        if ($lang == static::get_primary_language() || !in_array($lang, static::$lang_list))
            return false;

        return DB::update('language')->set([$lang => $translation])->where(static::get_primary_language(), '=', $string)->execute() > 0;
    }

    /**
     * Marks a string as missing in a certain language
     * @param string $string Missing string
     * @return bool Success
     */
    public static function set_missing($string) {
        if (static::$readonly) return true;

        $hash = md5($string, true);
        if (DB::select('id')->from('language')->where('hash','=', $hash)->execute()->count())
            return false;

        list($id, $rows) = DB::insert('language', ['hash',static::get_primary_language()])->values([$hash,$string])->execute();
        return $rows > 0;
    }

    /**
     * Returns a list of all missing strings for one language
     * @param string $lang Language
     * @return array
     */
    public static function get_missing($lang) {
        if ($lang == static::get_primary_language() || !in_array($lang, static::$lang_list))
            return [];

        return DB::select(static::get_primary_language())->from('language')->where($lang, '=', null)->execute()->as_array(null, static::get_primary_language());
    }

    /**
     * Removes a string from all translations as well as the cache and missing list. Legacy translations can not be removed!
     * @param string $string String to remove
     * @return bool Success
     */
    public static function remove($string) {
        static::flush_cache();

        return DB::delete('language')->where(static::get_primary_language(), '=', $string)->execute() > 0;
    }

    /**
     * Fetches a translation in a given language for a given string. If there is no translation, the same string will be returned. If the given string is not part of the translation database, it will be added to the missing strings list.
     * @param string $string String to translate
     * @param string|null $lang Language (null, to use default language)
     * @return string Translated string
     */
	public static function get($string, $lang = NULL) {
		// Return identity if input is something other than a string
        if (!is_string($string)) return $string;

        // Don't translate anything that begins with [nt]
		if (strpos($string, '[nt]') === 0)
			return str_replace('[nt]', '', $string);

        // Load default lang if none is given
		if ($lang == null) $lang = I18n::$lang;

        // Check of language is valid
        if ($lang == static::get_primary_language() || !in_array($lang, static::$lang_list))
            return $string;

        // Load language table
		$table = I18n::load($lang);

		// Return the translated string if it exists
        if (!isset($table[$string])) {
            static::set_missing($string);
            return $string;
        } elseif (!$table[$string])
            return $string;
        else return $table[$string];
	}
}