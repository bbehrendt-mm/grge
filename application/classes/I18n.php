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
    protected static $missing = array();
    protected static $got_missing = array();

    protected static $readonly = false;

	protected static $lang_list = array('de', 'en', 'es');

    /**
     * Returns a complete translation table
     * @param string|null $lang Language, or null to use default
     * @return array
     */
    public static function get_all($lang = NULL) {
        return I18n::load($lang);
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
     * @param string|array $string Original string
     * @param string|array $translation Translated string
     * @param string $lang Translation language
     * @return bool True when successfull, otherwise false
     */
    public static function set($string, $translation, $lang) {
        static::flush_cache();
        $table = I18n::load($lang);

        if (!is_array($string)) $string = [$string];
        if (!is_array($translation)) $translation = [$translation];

        foreach ($string as $n => $s) {
            if (!isset($table[$s])) return false;
            $table[$s] = isset($translation[$n]) ? $translation[$n] : $translation[count($translation)-1];
        }

        I18n::toDisk($lang, $table);
        static::flush_cache();
        return true;
    }

    /**
     * Marks a string as missing in a certain language
     * @param string $string Missing string
     * @param string $lang Language
     */
    public static function set_missing($string,$lang) {
        if (!isset(static::$missing[$lang]))
            static::$missing[$lang] = [];
        static::$missing[$lang][$string] = '';
    }

    /**
     * Returns a list of all missing strings for one language
     * @param string $lang Language
     * @return mixed
     */
    public static function get_missing($lang) {
        if (isset(static::$got_missing[$lang]))
            return static::$missing[$lang];
        static::$got_missing[$lang] = true;
        if (!isset(static::$missing[$lang]))
            static::$missing[$lang] = [];
        static::$missing[$lang] = array_merge(I18n::load("auto/$lang"),static::$missing[$lang]);
        return static::$missing[$lang];
    }

    /**
     * Removes a string from the list of missing language strings
     * @param string $string String to remove
     * @param string $lang Language
     * @return bool
     */
    public static function remove_missing($string, $lang) {
        static::get_missing($lang);
        if (isset(static::$missing[$lang][$string])) {
            unset(static::$missing[$lang][$string]);
            I18n::toDisk("auto/$lang", static::$missing[$lang]);
            return true;
        }
        return false;
    }

    /**
     * Removes a string from all translations as well as the cache and missing list. Legacy translations can not be removed!
     * @param string $string String to remove
     */
    public static function remove($string) {
        static::flush_cache();
        foreach (static::$lang_list as $lang) {
            static::remove_missing($string, $lang);

            $table = I18n::load($lang);
            unset(I18n::$cache[$string], $table[$string]);
            I18n::toDisk($lang, $table);
        }
        static::flush_cache();
    }

    /**
     * Fetches a translation in a given language for a given string. If there is no translation, the same string will be returned. If the given string is not part of the translation database, it will be added to the missing strings list.
     * @param string $string String to translate
     * @param string|null $lang Language (null, to use default language)
     * @param bool $pool True, if you want to fetch from the pool. If set to false, and the string is not found, this function will try to fetch it from the pool automatically and add it to the new translation file
     * @return string Translated string
     */
	public static function get($string, $lang = NULL, $pool = false) {
		// Return identity if input is something other than a string
        if (!is_string($string)) return $string;

        // Don't translate anything that begins with [nt]
		if (strpos($string, '[nt]') === 0)
			return str_replace('[nt]', '', $string);

        // Load default lang if none is given
		if ($lang == null) $lang = I18n::$lang;

        // Load language table
		$table = I18n::load(($pool ? 'pool/' : '') .$lang);
		I18n::$cache[$string] = $string;
		
		// Return the translated string if it exists
	    if(isset($table[$string])) return $table[$string];
	    elseif (!$pool) return static::get($string, $lang, true);
        else {
            static::set_missing($string,$lang);
            return $string;
        }
	}

    /**
     * Writes a language table to disc
     * @param string $lang Language
     * @param mixed $table Translation table
     */
	private static function toDisk($lang, $table) {
		if (static::$readonly) return;

        $contents = "<?php defined('SYSPATH') or die('No direct script access.');\n/* Automatically generated translation file for $lang */\n\nreturn ";
		$contents .= var_export($table, true);
		$contents .= ';';
		
		$contents = str_replace(' => ', " =>\n\t", $contents);
		file_put_contents(APPPATH.'/i18n/' . $lang . '.php', $contents);
	}

    /**
     * Write all pending data to their appropriate files
     */
	public static function write() {
        if (static::$readonly) return;

        $tables = array();
        foreach (I18n::$lang_list as $lang)
   			$tables[$lang] = I18n::load($lang);
   		
   		$full_lg = I18n::$cache;
   		
   		if (file_exists(APPPATH.'/i18n/import.lines')) foreach (explode("\n",file_get_contents(APPPATH.'/i18n/import.lines')) as $line) 
   			$full_lg[$line] = $line;
   		
   		foreach ($tables as $lang_table)
   			$full_lg = array_merge($full_lg, $lang_table);

   		$update = array();
   		foreach (array_keys($full_lg) as $key) if (!is_numeric($key) && $key != '') foreach ($tables as $lang => $table) {
   			if (!isset($table[$key])) {
   				$tables[$lang][$key] = static::get($key,$lang);
   				$update[$lang] = true;
   			}
   		}	
   			
   		foreach ($tables as $lang => $table) if (isset($update[$lang]))
   			I18n::toDisk($lang, $table);

        foreach (static::$missing as $lang => $table) if ($lang != 'de')
            I18n::toDisk("auto/$lang", static::get_missing($lang));
	}
}