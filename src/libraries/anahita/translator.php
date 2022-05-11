<?php

/**
 * Text  handling class
 *
 * @static
 * @package 	Anahita.Framework
 */
class AnTranslator
{
	/**
	 * Translates a string into the current language
	 *
	 * @access	public
	 * @param	string $string The string to translate
	 * @param	boolean	$jsSafe		Make the result javascript safe
	 * @since	4.3
	 *
	 */
	public static function _($string, $jsSafe = false)
	{
		$language = AnService::get('anahita:language');
		return $language->_($string, $jsSafe);
	}

	/**
	 * Passes a string thru an sprintf
	 *
	 * @access	public
	 * @param	format The format string
	 * @param	mixed Mixed number of arguments for the sprintf function
	 * @since	4.3
	 */
	public static function sprintf($string)
	{
		$language = AnService::get('anahita:language');
		$args = func_get_args();
		
		if (! empty($args)) {
			$args[0] = $language->_($args[0]);
			return call_user_func_array('sprintf', $args);
		}
		
		return '';
	}

	/**
	 * Passes a string thru an printf
	 *
	 * @access	public
	 * @param	format The format string
	 * @param	mixed Mixed number of arguments for the sprintf function
	 * @since	4.3
	 */
	public static function printf($string)
	{
		$language = AnService::get('anahita:language');
		$args = func_get_args();
		
		if (! empty($args)) {
			$args[0] = $language->_($args[0]);
			return call_user_func_array('printf', $args);
		}
		
		return '';
	}

}
