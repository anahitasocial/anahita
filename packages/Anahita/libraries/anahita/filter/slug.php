<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Slut Filter. A modification to the KFilterSlug that allows utf-8 characters 
 * in the slug
 *
 * @category   Anahita
 * @package    Anahita_Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnFilterSlug extends KFilterSlug 
{
    /**
     * The slut length
     * 
     * @var int
     */
    protected $_slug_length;
    
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_slug_length = $config->slug_length;
    }
        
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'slug_length' => 255
        ));
    
        parent::_initialize($config);
    }
        
	/**
	 * Validate a value
	 *
	 * @param scalar Value to be validated
	 * 
	 * @return bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		return $value === $this->_sanitize($value);
	}
	
	/**
	 * Sanitize a Value
	 *
	 * @param scalar Value to be validated
	 * 
	 * @return string The santizied value
	 */
	protected function _sanitize($value)
	{		
		$value = strip_tags($value);
		// Preserve escaped octets.
		$value = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $value);
		// Remove percent signs that are not part of an octet.
		$value = str_replace('%', '', $value);
		// Restore octets.
		$value = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $value);
		
		if ($this->_utf8($value)) {
			if (function_exists('mb_strtolower')) {
				$value = mb_strtolower($value, 'UTF-8');
			}
			$value = $this->_utf8encode($value, $this->_slug_length);
		}
	
		$value = strtolower($value);
		$value = preg_replace('/&.+?;/', '', $value); // kill entities
		$value = str_replace('.', '-', $value);
		$value = preg_replace('/[^%a-z0-9 _-]/', '', $value);
		$value = preg_replace('/\s+/', '-', $value);
		$value = preg_replace('|-+|', '-', $value);
		$value = trim($value, '-');
		return $value;
	}	
	
	/**
	 * Check if a string is UTF-8 or not. This method is take from Wodpress formatting.php method seems_utf8
	 *
	 * @param string $str The string to check
	 * 
	 * @return boolean Return true of the passed string is UTF-8 o/w returns false
	 */	
	protected function _utf8($str)
	{
		$length = strlen($str);
		for ($i=0; $i < $length; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) $n = 0; # 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;		
	}
	
	/**
	 * Encode the Unicode values to be used in the URI. 
	 * This method is take from Wodpress formatting.php method utf8_uri_encode
	 *
	 * @param string $string The string to be encoded in utf-8
	 * @param int    $length The max length 
	 * 
	 * @return string Return the utf-8 encoded string
	 */
	protected function _utf8encode($utf8_string, $length)
	{
		$unicode = '';
		$values = array();
		$num_octets = 1;
		$unicode_length = 0;
	
		$string_length = strlen( $utf8_string );
		for ($i = 0; $i < $string_length; $i++ ) {
	
			$value = ord( $utf8_string[ $i ] );
	
			if ( $value < 128 ) {
				if ( $length && ( $unicode_length >= $length ) )
					break;
				$unicode .= chr($value);
				$unicode_length++;
			} else {
				if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;
	
				$values[] = $value;
	
				if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
					break;
				if ( count( $values ) == $num_octets ) {
					if ($num_octets == 3) {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
						$unicode_length += 9;
					} else {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
						$unicode_length += 6;
					}
	
					$values = array();
					$num_octets = 1;
				}
			}
		}
		
		return $unicode;		
	}		
}