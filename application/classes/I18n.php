<?php 

if ( ! function_exists('__'))
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
     * @param array $values values to replace in the translated text
     * @param string $lang source language
     * @return  string
     */
	function __($string, array $values = null, $lang = 'de-de')
	{

        $values = array_merge(empty($values) ? [] : $values, [
            //Defaults
            '::i::' => '<i>',
            '::/i::' => '</i>',
            '::b::' => '<b>',
            '::/b::' => '</b>'
        ]);

		return empty($values) ? I18n::get($string) : strtr(I18n::get($string), $values);
	}
}

class I18n extends Kohana_I18n {
	// Cache of missing strings
	protected static $cache = array();
	protected static $lang_list = array('de', 'en');
 
	public static function get($string, $lang = NULL) {				
		if (!is_string($string)) return $string;
				
		if (strpos($string, '[nt]') === 0)
			return str_replace('[nt]', '', $string);
		
		if ($lang == null) $lang = I18n::$lang;

		$table = I18n::load($lang);
		I18n::$cache[$string] = $string;
		
		// Return the translated string if it exists
	    if(isset($table[$string])) return $table[$string];
	    else return $string;
	}
 
	private static function toDisk($lang, $table) {
		$contents = "<?php defined('SYSPATH') or die('No direct script access.');\n/* Automatically generated translation file for $lang */\n\nreturn ";
		$contents .= var_export($table, true);
		$contents .= ';';
		
		$contents = str_replace(' => ', " =>\n\t", $contents);
		file_put_contents(APPPATH.'/i18n/' . $lang . '.php', $contents);
	}
	
	public static function write() {
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
   				$tables[$lang][$key] = $key;
   				$update[$lang] = true;
   			}
   		}	
   			
   		foreach ($tables as $lang => $table) if (isset($update[$lang]))
   			I18n::toDisk($lang, $table);
	}
}