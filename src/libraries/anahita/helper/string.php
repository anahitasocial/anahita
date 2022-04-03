<?php
/**
 * @package     AnHelper
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://www.Anahita.io
 */

/**
 * PHP mbstring and iconv local configuration
 */

// check if mbstring extension is loaded and attempt to load it if not present except for windows
if (extension_loaded('mbstring') || ((!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && dl('mbstring.so')))) {
    //Make sure to surpress the output in case ini_set is disabled
    @ini_set('mbstring.internal_encoding', 'UTF-8');
    @ini_set('mbstring.http_input', 'UTF-8');
    @ini_set('mbstring.http_output', 'UTF-8');
}

// check if iconv extension is loaded and attempt to load it if not present except for windows
if (function_exists('iconv') || ((!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && dl('iconv.so')))) {
    // these are settings that can be set inside code
    iconv_set_encoding("internal_encoding", "UTF-8");
    iconv_set_encoding("input_encoding", "UTF-8");
    iconv_set_encoding("output_encoding", "UTF-8");
}

/**
 * String helper class for utf-8 data
 *
 * All functions assume the validity of utf-8 strings.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Anahita
 * @package     Anahita_Helper
 * @subpackage  String
 * @static
 */
class AnHelperString
{
    /**
     * UTF-8 aware alternative to strpos
     *
     * Find position of first occurrence of a string
     *
     * @param $str - string String being examined
     * @param $search - string String being searced for
     * @param $offset - int Optional, specifies the position from which the search should be performed
     * @return mixed Number of characters before the first match or FALSE on failure
     * @see http://www.php.net/strpos
     */
    public static function strpos($str, $search, $offset = false)
    {
        if (strlen($str) && strlen($search)) {
            if ($offset === false) {
                return mb_strpos($str, $search);
            } else {
                return mb_strpos($str, $search, $offset);
            }
        } else {
            return false;
        }
    }

    /**
     * UTF-8 aware alternative to strrpos
     *
     * Finds position of last occurrence of a string
     *
     * @param $str - string String being examined
     * @param $search - string String being searced for
     * @return mixed Number of characters before the last match or FALSE on failure
     * @see http://www.php.net/strrpos
     */
    public static function strrpos($str, $search)
    {
        if ($offset === false) {
            # Emulate behaviour of strrpos rather than raising warning
            if (empty($str)) {
                return false;
            }
            return mb_strrpos($str, $search);
        } else {
            if (! is_int($offset)) {
                trigger_error('utf8_strrpos expects parameter 3 to be long', E_USER_WARNING);
                return false;
            }

            $str = mb_substr($str, $offset);

            if (false !== ($pos = mb_strrpos($str, $search))) {
                return $pos + $offset;
            }

            return false;
        }
    }

    /**
     * UTF-8 aware alternative to substr
     *
     * Return part of a string given character offset (and optionally length)
     *
     * @param string
     * @param integer number of UTF-8 characters offset (from left)
     * @param integer (optional) length in UTF-8 characters from offset
     * @return mixed string or FALSE if failure
     * @see http://www.php.net/substr
     */
    public static function substr($str, $offset, $length = false)
    {
        if ($length === false) {
            return mb_substr($str, $offset);
        } else {
            return mb_substr($str, $offset, $length);
        }
    }

    /**
     * UTF-8 aware alternative to strtlower
     *
     * Make a string lowercase
     *
     * Note: The concept of a characters "case" only exists is some alphabets
     * such as Latin, Greek, Cyrillic, Armenian and archaic Georgian - it does
     * not exist in the Chinese alphabet, for example. See Unicode Standard
     * Annex #21: Case Mappings
     *
     * @param string
     * @return mixed either string in lowercase or FALSE is UTF-8 invalid
     * @see http://www.php.net/strtolower
     */
    public static function strtolower($str)
    {
        return mb_strtolower($str);
    }

    /**
     * UTF-8 aware alternative to strtoupper
     *
     * Make a string uppercase
     *
     * Note: The concept of a characters "case" only exists is some alphabets
     * such as Latin, Greek, Cyrillic, Armenian and archaic Georgian - it does
     * not exist in the Chinese alphabet, for example. See Unicode Standard
     * Annex #21: Case Mappings
     *
     * @param string
     * @return mixed either string in uppercase or FALSE is UTF-8 invalid
     * @see http://www.php.net/strtoupper
     */
    public static function strtoupper($str)
    {
        return mb_strtoupper($str);
    }

    /**
     * UTF-8 aware alternative to strlen
     *
     * Returns the number of characters in the string (NOT THE NUMBER OF BYTES),
     *
     * @param string UTF-8 string
     * @return int number of UTF-8 characters in string
     * @see http://www.php.net/strlen
     */
    public static function strlen($str)
    {
        return mb_strlen($str);
    }

    /**
     * UTF-8 aware alternative to str_ireplace
     *
     * Case-insensitive version of str_replace
     *
     * @param string string to search
     * @param string existing string to replace
     * @param string new string to replace with
     * @param int optional count value to be passed by referene
     * @see http://www.php.net/str_ireplace
    */
    public static function str_ireplace($search, $replace, $str, $count = null)
    {
        if (! is_array($search)) {
            $slen = strlen($search);
            $lendif = strlen($replace) - $slen;
            if ($slen === 0) {
                return $str;
            }

            $search = AnHelperString::strtolower($search);

            $search = preg_quote($search, '/');
            $lstr = AnHelperString::strtolower($str);
            $i = 0;
            $matched = 0;
            while (preg_match('/(.*)'.$search.'/Us', $lstr, $matches)) {
                if ($i === $count) {
                    break;
                }
                $mlen = strlen($matches[0]);
                $lstr = substr($lstr, $mlen);
                $str = substr_replace($str, $replace, $matched+strlen($matches[1]), $slen);
                $matched += $mlen + $lendif;
                $i++;
            }
            return $str;
        } else {
            foreach (array_keys($search) as $k) {
                if (is_array($replace)) {
                    if (array_key_exists($k, $replace)) {
                        $str = AnHelperString::str_ireplace($search[$k], $replace[$k], $str, $count);
                    } else {
                        $str = AnHelperString::str_ireplace($search[$k], '', $str, $count);
                    }
                } else {
                    $str = AnHelperString::str_ireplace($search[$k], $replace, $str, $count);
                }
            }
            return $str;
        }
    }

    /**
     * UTF-8 aware alternative to str_split
     *
     * Convert a string to an array
     *
     * @param string UTF-8 encoded
     * @param int number to characters to split string by
     * @return array
     * @see http://www.php.net/str_split
    */
    public static function str_split($str, $split_len = 1)
    {
        if (! preg_match('/^[0-9]+$/', $split_len) || $split_len < 1) {
            return false;
        }

        $len = AnHelperString::strlen($str);
        if ($len <= $split_len) {
            return array($str);
        }

        preg_match_all('/.{'.$split_len.'}|[^\x00]{1,'.$split_len.'}$/us', $str, $ar);
        return $ar[0];
    }

    /**
     * UTF-8 aware alternative to strcasecmp
     *
     * A case insensivite string comparison
     *
     * @param string string 1 to compare
     * @param string string 2 to compare
     * @return int < 0 if str1 is less than str2; > 0 if str1 is greater than str2, and 0 if they are equal.
     * @see http://www.php.net/strcasecmp
    */
    public static function strcasecmp($str1, $str2)
    {
        $strX = AnHelperString::strtolower($strX);
        $strY = AnHelperString::strtolower($strY);
        
        return strcmp($strX, $strY);
    }

    /**
     * UTF-8 aware alternative to strcspn
     * Find length of initial segment not matching mask
     *
     * @param string
     * @param string the mask
     * @param int Optional starting character position (in characters)
     * @param int Optional length
     * @return int the length of the initial segment of str1 which does not contain any of the characters in str2
     * @see http://www.php.net/strcspn
    */
    public static function strcspn($str, $mask, $start = null, $length = null)
    {
        if (empty($mask) || strlen($mask) == 0) {
            return null;
        }

        $mask = preg_replace('!([\\\\\\-\\]\\[/^])!', '\\\${1}', $mask);

        if ($start !== null || $length !== null) {
            $str = AnHelperString::substr($str, $start, $length);
        }

        preg_match('/^[^'.$mask.']+/u', $str, $matches);

        if (isset($matches[0])) {
            return utf8_strlen($matches[0]);
        }

        return 0;
    }

    /**
     * UTF-8 aware alternative to stristr
     *
     * Returns all of haystack from the first occurrence of needle to the end.
     * needle and haystack are examined in a case-insensitive manner
     * Find first occurrence of a string using case insensitive comparison
     *
     * @param string the haystack
     * @param string the needle
     * @return string the sub string
     * @see http://www.php.net/stristr
    */
    public static function stristr($str, $search)
    {
        if (strlen($search) === 0) {
            return $str;
        }

        $lstr = AnHelperString::strtolower($str);
        $lsearch = AnHelperString::strtolower($search);
        preg_match('|^(.*)'.preg_quote($lsearch).'|Us', $lstr, $matches);

        if (count($matches) === 2) {
            return substr($str, strlen($matches[1]));
        }

        return false;
    }

    /**
     * UTF-8 aware alternative to strrev
     *
     * Reverse a string
     *
     * @param string String to be reversed
     * @return string The string in reverse character order
     * @see http://www.php.net/strrev
     */
    public static function strrev($str)
    {
        preg_match_all('/./us', $str, $ar);
        return join('', array_reverse($ar[0]));
    }

    /**
     * UTF-8 aware alternative to strspn
     *
     * Find length of initial segment matching mask
     *
     * @param string the haystack
     * @param string the mask
     * @param int start optional
     * @param int length optional
     * @see http://www.php.net/strspn
    */
    public static function strspn($str, $mask, $start = null, $length = null)
    {
        $mask = preg_replace('!([\\\\\\-\\]\\[/^])!', '\\\${1}', $mask);

        if ($start !== null || $length !== null) {
            $str = AnHelperString::substr($str, $start, $length);
        }

        preg_match('/^['.$mask.']+/u', $str, $matches);

        if (isset($matches[0])) {
            return AnHelperString::strlen($matches[0]);
        }

        return 0;
    }

    /**
     * UTF-8 aware substr_replace
     *
     * Replace text within a portion of a string
     *
     * @param string the haystack
     * @param string the replacement string
     * @param int start
     * @param int length (optional)
     * @see http://www.php.net/substr_replace
    */
    public static function substr_replace($str, $repl, $start, $length = null)
    {
        preg_match_all('/./us', $str, $ar);
        preg_match_all('/./us', $repl, $rar);
        
        if ($length === null) {
            $length = AnHelperString::strlen($str);
        }
        
        array_splice($ar[0], $start, $length, $rar[0]);
        return join('', $ar[0]);
    }

    /**
     * UTF-8 aware replacement for ltrim()
     *
     * Strip whitespace (or other characters) from the beginning of a string
     * Note: you only need to use this if you are supplying the charlist
     * optional arg and it contains UTF-8 characters. Otherwise ltrim will
     * work normally on a UTF-8 string
     *
     * @param string the string to be trimmed
     * @param string the optional charlist of additional characters to trim
     * @return string the trimmed string
     * @see http://www.php.net/ltrim
    */
    public static function ltrim($str, $charlist = false)
    {
        if ($charlist === false) {
            return ltrim($str);
        }

        //quote charlist for use in a characterclass
        $charlist = preg_replace('!([\\\\\\-\\]\\[/^])!', '\\\${1}', $charlist);

        return preg_replace('/^['.$charlist.']+/u', '', $str);
    }

    /**
     * UTF-8 aware replacement for rtrim()
     *
     * Strip whitespace (or other characters) from the end of a string
     * Note: you only need to use this if you are supplying the charlist
     * optional arg and it contains UTF-8 characters. Otherwise rtrim will
     * work normally on a UTF-8 string
     *
     * @param string the string to be trimmed
     * @param string the optional charlist of additional characters to trim
     * @return string the trimmed string
     * @see http://www.php.net/rtrim
    */
    public static function rtrim($str, $charlist = false)
    {
        if ($charlist === false) {
            return rtrim($str);
        }

        //quote charlist for use in a characterclass
        $charlist = preg_replace('!([\\\\\\-\\]\\[/^])!', '\\\${1}', $charlist);

        return preg_replace('/['.$charlist.']+$/u', '', $str);
    }

    /**
     * UTF-8 aware replacement for trim()
     *
     * Strip whitespace (or other characters) from the beginning and end of a string
     * Note: you only need to use this if you are supplying the charlist
     * optional arg and it contains UTF-8 characters. Otherwise trim will
     * work normally on a UTF-8 string
     *
     * @param string the string to be trimmed
     * @param string the optional charlist of additional characters to trim
     * @return string the trimmed string
     * @see http://www.php.net/trim
    */
    public static function trim($str, $charlist = false)
    {
        if ($charlist === false) {
            return trim($str);
        }

        return AnHelperString::ltrim(utf8_rtrim($str, $charlist), $charlist);
    }

    /**
     * UTF-8 aware alternative to ucfirst
     *
     * Make a string's first character uppercase
     *
     * @param string
     * @return string with first character as upper case (if applicable)
     * @see http://www.php.net/ucfirst
    */
    public static function ucfirst($str)
    {
        switch (AnHelperString::strlen($str)) {
            case 0:
                return '';
            break;
            case 1:
                return AnHelperString::strtoupper($str);
            break;
            default:
                preg_match('/^(.{1})(.*)$/us', $str, $matches);
                return AnHelperString::strtoupper($matches[1]).$matches[2];
            break;
        }
    }

    /**
     * UTF-8 aware alternative to ucwords
     *
     * Uppercase the first character of each word in a string
     *
     * @param string
     * @return string with first char of each word uppercase
     * @see http://www.php.net/ucwords
    */
    public static function ucwords($str)
    {
        // Note: [\x0c\x09\x0b\x0a\x0d\x20] matches;
        // form feeds, horizontal tabs, vertical tabs, linefeeds and carriage returns
        // This corresponds to the definition of a "word" defined at http://www.php.net/ucwords
        $pattern = '/(^|([\x0c\x09\x0b\x0a\x0d\x20]+))([^\x0c\x09\x0b\x0a\x0d\x20]{1})[^\x0c\x09\x0b\x0a\x0d\x20]*/u';
        return preg_replace_callback($pattern, 'AnHelperString::ucwords_callback', $str);
    }

    /**
     * Callback function for preg_replace_callback call in utf8_ucwords
     *
     * You don't need to call this yourself
     *
     * @param array of matches corresponding to a single word
     * @return string with first char of the word in uppercase
     * @see ucwords
     * @see strtoupper
     */
    public static function ucwords_callback($matches)
    {
        $leadingws = $matches[2];
        $ucfirst = AnHelperString::strtoupper($matches[3]);
        $ucword = AnHelperString::substr_replace(ltrim($matches[0]), $ucfirst, 0, 1);
        return $leadingws . $ucword;
    }

    /**
     * Transcode a string.
     *
     * @param string $source The string to transcode.
     * @param string $from_encoding The source encoding.
     * @param string $to_encoding The target encoding.
     * @return string Transcoded string
     */
    public static function transcode($source, $from_encoding, $to_encoding)
    {
        if (is_string($source)) {
            /*
             * "//TRANSLIT" is appendd to the $to_encoding to ensure that when iconv comes
             * across a character that cannot be represented in the target charset, it can
             * be approximated through one or several similarly looking characters.
             */
            return iconv($from_encoding, $to_encoding.'//TRANSLIT', $source);
        }
    }

    /**
     * Tests a string as to whether it's valid UTF-8 and supported by the Unicode standard
     *
     * Note: this function has been modified to simple return true or false
     *
     * @author <hsivonen@iki.fi>
     * @param string UTF-8 encoded string
     * @return boolean true if valid
     * @see http://hsivonen.iki.fi/php-utf8/
     * @see compliant
     */
    public static function valid($str)
    {
        $mState = 0;     // cached expected number of octets after the current octet
                         // until the beginning of the next UTF8 character sequence
        $mUcs4  = 0;     // cached Unicode character
        $mBytes = 1;     // cached expected number of octets in the current sequence

        $len = strlen($str);

        for ($i = 0; $i < $len; $i++) {
            $in = ord($str{$i});

            if ($mState == 0) {
                // When mState is zero we expect either a US-ASCII character or a
                // multi-octet sequence.
                if (0 == (0x80 & ($in))) {
                    // US-ASCII, pass straight through.
                    $mBytes = 1;
                } elseif (0xC0 == (0xE0 & ($in))) {
                    // First octet of 2 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x1F) << 6;
                    $mState = 1;
                    $mBytes = 2;
                } elseif (0xE0 == (0xF0 & ($in))) {
                    // First octet of 3 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x0F) << 12;
                    $mState = 2;
                    $mBytes = 3;
                } elseif (0xF0 == (0xF8 & ($in))) {
                    // First octet of 4 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x07) << 18;
                    $mState = 3;
                    $mBytes = 4;
                } elseif (0xF8 == (0xFC & ($in))) {
                    /* First octet of 5 octet sequence.
                     *
                     * This is illegal because the encoded codepoint must be either
                     * (a) not the shortest form or
                     * (b) outside the Unicode range of 0-0x10FFFF.
                     * Rather than trying to resynchronize, we will carry on until the end
                     * of the sequence and let the later error handling code catch it.
                     */
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x03) << 24;
                    $mState = 4;
                    $mBytes = 5;
                } elseif (0xFC == (0xFE & ($in))) {
                    // First octet of 6 octet sequence, see comments for 5 octet sequence.
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 1) << 30;
                    $mState = 5;
                    $mBytes = 6;
                } else {
                    /* Current octet is neither in the US-ASCII range nor a legal first
                     * octet of a multi-octet sequence.
                     */
                    return false;
                }
            } else {
                // When mState is non-zero, we expect a continuation of the multi-octet
                // sequence
                if (0x80 == (0xC0 & ($in))) {
                    // Legal continuation.
                    $shift = ($mState - 1) * 6;
                    $tmp = $in;
                    $tmp = ($tmp & 0x0000003F) << $shift;
                    $mUcs4 |= $tmp;

                    /**
                     * End of the multi-octet sequence. mUcs4 now contains the final
                     * Unicode codepoint to be output
                     */
                    if (0 == --$mState) {
                        /*
                         * Check for illegal sequences and codepoints.
                         */
                        // From Unicode 3.1, non-shortest form is illegal
                        if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
                            ((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
                            ((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
                            (4 < $mBytes) ||
                            // From Unicode 3.2, surrogate characters are illegal
                            (($mUcs4 & 0xFFFFF800) == 0xD800) ||
                            // Codepoints outside the Unicode range are illegal
                            ($mUcs4 > 0x10FFFF)) {
                            return false;
                        }

                        //initialize UTF8 cache
                        $mState = 0;
                        $mUcs4  = 0;
                        $mBytes = 1;
                    }
                } else {
                    /**
                     *((0xC0 & (*in) != 0x80) && (mState != 0))
                     * Incomplete multi-octet sequence.
                     */
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Tests whether a string complies as UTF-8. This will be much
     * faster than utf8_is_valid but will pass five and six octet
     * UTF-8 sequences, which are not supported by Unicode and
     * so cannot be displayed correctly in a browser. In other words
     * it is not as strict as utf8_is_valid but it's faster. If you use
     * is to validate user input, you place yourself at the risk that
     * attackers will be able to inject 5 and 6 byte sequences (which
     * may or may not be a significant risk, depending on what you are
     * are doing)
     *
     * @see valid
     * @see http://www.php.net/manual/en/reference.pcre.pattern.modifiers.php#54805
     * @param string UTF-8 string to check
     * @return boolean TRUE if string is valid UTF-8
     */
    public static function compliant($str)
    {
        if (strlen($str) == 0) {
            return true;
        }
        // If even just the first character can be matched, when the /u
        // modifier is used, then it's valid UTF-8. If the UTF-8 is somehow
        // invalid, nothing at all will match, even if the string contains
        // some valid sequences
        return (preg_match('/^.{1}/us', $str, $ar) == 1);
    }
}
