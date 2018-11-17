<?php

 /**
  * @package     Anahita_Filter
  * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
  * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
  * @copyright   Copyright (C) 2013 Arash Sanieyan <ash@anahitapolis.com>
  * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
  * @link        http://www.nooku.org
  * @link        https://www.GetAnahita.com
  */
  
class AnFilterSlug extends AnFilterAbstract
{
    /**
     * The slut length.
     *
     * @var int
     */
    protected $_slug_length;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        $this->_slug_length = $config->slug_length;
        $this->_length    = $config->length;
		$this->_separator = $config->separator;
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'slug_length' => 255,
            'separator' => '-',
    		'length' 	=> 255
        ));

        parent::_initialize($config);
    }

    /**
     * Validate a value.
     *
     * @param scalar Value to be validated
     *
     * @return bool True when the variable is valid
     */
    protected function _validate($value)
    {
        return $value === $this->_sanitize($value);
    }

    /**
     * Sanitize a Value.
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
        $value = preg_replace('/&.+?;|[^%a-z0-9_-]/', '-', $value); // replace entities and non-alpahnum with dashes
        $value = preg_replace('|-+|', '-', $value);
        $value = trim($value, '-');

        return $value;
    }

    /**
     * Check if a string is UTF-8 or not. This method is take from Wodpress formatting.php method seems_utf8.
     *
     * @param string $str The string to check
     *
     * @return bool Return true of the passed string is UTF-8 o/w returns false
     */
    protected function _utf8( $str ) {

        mbstring_binary_safe_encoding();

        $length = strlen($str);

        reset_mbstring_encoding();

        for ($i=0; $i < $length; $i++) {
    		$c = ord($str[$i]);
    		if ($c < 0x80) $n = 0; // 0bbbbbbb
    		elseif (($c & 0xE0) == 0xC0) $n=1; // 110bbbbb
    		elseif (($c & 0xF0) == 0xE0) $n=2; // 1110bbbb
    		elseif (($c & 0xF8) == 0xF0) $n=3; // 11110bbb
    		elseif (($c & 0xFC) == 0xF8) $n=4; // 111110bb
    		elseif (($c & 0xFE) == 0xFC) $n=5; // 1111110b
    		else return false; // Does not match any model
    		for ($j=0; $j<$n; $j++) { // n bytes matching 10bbbbbb follow ?
    			if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
    				return false;
    		}
    	}

    	return true;
    }

    /**
     * Encode the Unicode values to be used in the URI.
     * This method is take from Wodpress formatting.php method utf8_uri_encode.
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
    	mbstring_binary_safe_encoding();
    	$string_length = strlen( $utf8_string );
    	reset_mbstring_encoding();
    	for ($i = 0; $i < $string_length; $i++ ) {
    		$value = ord( $utf8_string[ $i ] );
    		if ( $value < 128 ) {
    			if ( $length && ( $unicode_length >= $length ) )
    				break;
    			$unicode .= chr($value);
    			$unicode_length++;
    		} else {
    			if ( count( $values ) == 0 ) {
    				if ( $value < 224 ) {
    					$num_octets = 2;
    				} elseif ( $value < 240 ) {
    					$num_octets = 3;
    				} else {
    					$num_octets = 4;
    				}
    			}
    			$values[] = $value;
    			if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
    				break;
    			if ( count( $values ) == $num_octets ) {
    				for ( $j = 0; $j < $num_octets; $j++ ) {
    					$unicode .= '%' . dechex( $values[ $j ] );
    				}
    				$unicode_length += $num_octets * 3;
    				$values = array();
    				$num_octets = 1;
    			}
    		}
    	}

    	return $unicode;
    }
}
