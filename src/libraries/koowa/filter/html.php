<?php
/**
* @version		$Id: html.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Html XSS Filter
 *
 * Forked from the php input filter library by: Daniel Morris <dan@rootcube.com>
 * Original Contributors: Gianpaolo Racca, Ghislain Picard, Marco Wandschneider,
 * Chris Tobin.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterHtml extends KFilterAbstract
{
    /**
     * List of user-defined tags
     *
     * @var array
     */
    protected $_tagsArray = array();

    /**
     * List of user-defined attributes
     *
     * @var array
     */
    protected $_attrArray = array();

    /**
     * If false, use whiteList method, if true use blackList method
     *
     * @var boolean
     */
    protected $_tagsMethod = true;

    /**
     * If false, use whiteList method, if true use blackList method
     *
     * @var boolean
     */
    protected $_attrMethod = true;

    /**
     * If true, only auto clean essentials, if false allow clean blacklisted tags/attr
     *
     * @var boolean
     */
    protected $_xssAuto = true;


    protected $_tagBlacklist = array ('applet', 'body', 'bgsound', 'base', 'basefont', 'embed', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'object', 'script', 'style', 'title', 'xml');
    protected $_attrBlacklist = array ('action', 'background', 'codebase', 'dynsrc', 'lowsrc'); // also will strip ALL event handlers

    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // List of user-defined tags
        if(isset($config->tag_list)) {
            $this->_tagsArray = array_map('strtolower', (array) $config->tag_list);
        }

        // List of user-defined attributes
        if(isset($config->attribute_list)) {
            $this->_attrArray = array_map('strtolower', (array) $config->attribute_list);
        }

        // WhiteList method = 0, BlackList method = 1
        if(isset($config->tag_method)) {
            $this->_tagsMethod = $config->tag_method;
        }

        // WhiteList method = 0, BlackList method = 1
        if(isset($config->attribute_method)) {
            $this->_attrMethod = $config->attribute_method;
        }

        //If false, only auto clean essentials, if true allow clean blacklisted tags/attr
        if(isset($config->xss_auto)) {
            $this->_xssAuto = $config->xss_auto;
        }
    }

    /**
     * Validate a value
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return (is_string($value)
        // this is too strict, html is usually sanitized
        //&& strcmp($value, $this->sanitize($value)) === 0
        );
    }

    /**
     * Sanitize a value
     *
     * @param   scalar  Input string/array-of-string to be 'cleaned'
     * @return  mixed   'Cleaned' version of input parameter
     */
    protected function _sanitize($value)
    {
        $value = (string) $value;

        // Filter var for XSS and other 'bad' code etc.
        if (!empty ($value)) {
            $value = $this->_remove($this->_decode($value));
        }

        return $value;
    }

    /**
     * Internal method to iteratively remove all unwanted tags and attributes
     *
     * @param   string  $source Input string to be 'cleaned'
     * @return  string  'Cleaned' version of input parameter
     */
    protected function _remove($source)
    {
        $loopCounter = 0;

        // Iteration provides nested tag protection
        while ($source != $this->_cleanTags($source))
        {
            $source = $this->_cleanTags($source);
            $loopCounter ++;
        }
        return $source;
    }

    /**
     * Internal method to strip a string of certain tags
     *
     * @param   string  $source Input string to be 'cleaned'
     * @return  string  'Cleaned' version of input parameter
     */
    protected function _cleanTags($source)
    {
        $preTag         = null;
        $postTag        = $source;
        $currentSpace   = false;
        $attr           = '';

        // Is there a tag? If so it will certainly start with a '<'
        $tagOpen_start  = strpos($source, '<');

        while ($tagOpen_start !== false)
        {
            // Get some information about the tag we are processing
            $preTag         .= substr($postTag, 0, $tagOpen_start);
            $postTag        = substr($postTag, $tagOpen_start);
            $fromTagOpen    = substr($postTag, 1);
            $tagOpen_end    = strpos($fromTagOpen, '>');

            // Let's catch any non-terminated tags and skip over them
            if ($tagOpen_end === false) {
                $postTag        = substr($postTag, $tagOpen_start +1);
                $tagOpen_start  = strpos($postTag, '<');
                continue;
            }

            // Do we have a nested tag?
            $tagOpen_nested = strpos($fromTagOpen, '<');
            $tagOpen_nested_end = strpos(substr($postTag, $tagOpen_end), '>');
            if (($tagOpen_nested !== false) && ($tagOpen_nested < $tagOpen_end)) {
                $preTag         .= substr($postTag, 0, ($tagOpen_nested +1));
                $postTag        = substr($postTag, ($tagOpen_nested +1));
                $tagOpen_start  = strpos($postTag, '<');
                continue;
            }

            // Lets get some information about our tag and setup attribute pairs
            $tagOpen_nested = (strpos($fromTagOpen, '<') + $tagOpen_start +1);
            $currentTag     = substr($fromTagOpen, 0, $tagOpen_end);
            $tagLength      = strlen($currentTag);
            $tagLeft        = $currentTag;
            $attrSet        = array ();
            $currentSpace   = strpos($tagLeft, ' ');

            // Are we an open tag or a close tag?
            if (substr($currentTag, 0, 1) == '/') {
                // Close Tag
                $isCloseTag     = true;
                list ($tagName) = explode(' ', $currentTag);
                $tagName        = substr($tagName, 1);
            } else {
                // Open Tag
                $isCloseTag     = false;
                list ($tagName) = explode(' ', $currentTag);
            }

            /*
             * Exclude all "non-regular" tagnames
             * OR no tagname
             * OR remove if xssauto is on and tag is blacklisted
             */
            if ((!preg_match("/^[a-z][a-z0-9]*$/i", $tagName)) || (!$tagName) || ((in_array(strtolower($tagName), $this->_tagBlacklist)) && ($this->_xssAuto))) {
                $postTag        = substr($postTag, ($tagLength +2));
                $tagOpen_start  = strpos($postTag, '<');
                // Strip tag
                continue;
            }

            /*
             * Time to grab any attributes from the tag... need this section in
             * case attributes have spaces in the values.
             */
            while ($currentSpace !== false)
            {
                $attr           = '';
                $fromSpace      = substr($tagLeft, ($currentSpace +1));
                $nextSpace      = strpos($fromSpace, ' ');
                $openQuotes     = strpos($fromSpace, '"');
                $closeQuotes    = strpos(substr($fromSpace, ($openQuotes +1)), '"') + $openQuotes +1;

                // Do we have an attribute to process? [check for equal sign]
                if (strpos($fromSpace, '=') !== false) {
                    /*
                     * If the attribute value is wrapped in quotes we need to
                     * grab the substring from the closing quote, otherwise grab
                     * till the next space
                     */
                    if (($openQuotes !== false) && (strpos(substr($fromSpace, ($openQuotes +1)), '"') !== false)) {
                        $attr = substr($fromSpace, 0, ($closeQuotes +1));
                    } else {
                        $attr = substr($fromSpace, 0, $nextSpace);
                    }
                } else {
                    /*
                     * No more equal signs so add any extra text in the tag into
                     * the attribute array [eg. checked]
                     */
                    if ($fromSpace != '/') {
                        $attr = substr($fromSpace, 0, $nextSpace);
                    }
                }

                // Last Attribute Pair
                if (!$attr && $fromSpace != '/') {
                    $attr = $fromSpace;
                }

                // Add attribute pair to the attribute array
                $attrSet[] = $attr;

                // Move search point and continue iteration
                $tagLeft        = substr($fromSpace, strlen($attr));
                $currentSpace   = strpos($tagLeft, ' ');
            }

            // Is our tag in the user input array?
            $tagFound = in_array(strtolower($tagName), $this->_tagsArray);

            // If the tag is allowed lets append it to the output string
            if ((!$tagFound && $this->_tagsMethod) || ($tagFound && !$this->_tagsMethod)) {

                // Reconstruct tag with allowed attributes
                if (!$isCloseTag) {
                    // Open or Single tag
                    $attrSet = $this->_cleanAttributes($attrSet);
                    $preTag .= '<'.$tagName;
                    for ($i = 0; $i < count($attrSet); $i ++)
                    {
                        $preTag .= ' '.$attrSet[$i];
                    }

                    // Reformat single tags to XHTML
                    if (strpos($fromTagOpen, '</'.$tagName)) {
                        $preTag .= '>';
                    } else {
                        $preTag .= ' />';
                    }
                } else {
                    // Closing Tag
                    $preTag .= '</'.$tagName.'>';
                }
            }

            // Find next tag's start and continue iteration
            $postTag        = substr($postTag, ($tagLength +2));
            $tagOpen_start  = strpos($postTag, '<');
        }

        // Append any code after the end of tags and return
        if ($postTag != '<') {
            $preTag .= $postTag;
        }
        return $preTag;
    }

    /**
     * Internal method to strip a tag of certain attributes
     *
     * @param   array   $attrSet    Array of attribute pairs to filter
     * @return  array   Filtered array of attribute pairs
     */
    protected function _cleanAttributes($attrSet)
    {
        // Initialize variables
        $newSet = array();

        // Iterate through attribute pairs
        for ($i = 0; $i < count($attrSet); $i ++)
        {
            // Skip blank spaces
            if (!$attrSet[$i]) {
                continue;
            }

            // Split into name/value pairs
            $attrSubSet = explode('=', trim($attrSet[$i]), 2);
            list ($attrSubSet[0]) = explode(' ', $attrSubSet[0]);

            /*
             * Remove all "non-regular" attribute names
             * AND blacklisted attributes
             */
            if ((!preg_match('/[a-z]*$/i', $attrSubSet[0])) || (($this->_xssAuto) && ((in_array(strtolower($attrSubSet[0]), $this->_attrBlacklist)) || (substr($attrSubSet[0], 0, 2) == 'on')))) {
                continue;
            }

            // XSS attribute value filtering
            if ($attrSubSet[1]) {
                // strips unicode, hex, etc
                $attrSubSet[1] = str_replace('&#', '', $attrSubSet[1]);
                // strip normal newline within attr value
                $attrSubSet[1] = preg_replace('/[\n\r]/', '', $attrSubSet[1]);
                // strip double quotes
                $attrSubSet[1] = str_replace('"', '', $attrSubSet[1]);
                // convert single quotes from either side to doubles (Single quotes shouldn't be used to pad attr value)
                if ((substr($attrSubSet[1], 0, 1) == "'") && (substr($attrSubSet[1], (strlen($attrSubSet[1]) - 1), 1) == "'")) {
                    $attrSubSet[1] = substr($attrSubSet[1], 1, (strlen($attrSubSet[1]) - 2));
                }
                // strip slashes
                $attrSubSet[1] = stripslashes($attrSubSet[1]);
            }

            // Autostrip script tags
            if ($this->_checkAttribute($attrSubSet)) {
                continue;
            }

            // Is our attribute in the user input array?
            $attrFound = in_array(strtolower($attrSubSet[0]), $this->_attrArray);

            // If the tag is allowed lets keep it
            if ((!$attrFound && $this->_attrMethod) || ($attrFound && !$this->_attrMethod)) {

                // Does the attribute have a value?
                if ($attrSubSet[1]) {
                    $newSet[] = $attrSubSet[0].'="'.$attrSubSet[1].'"';
                } elseif ($attrSubSet[1] == "0") {
                    /*
                     * Special Case
                     * Is the value 0?
                     */
                    $newSet[] = $attrSubSet[0].'="0"';
                } else {
                    $newSet[] = $attrSubSet[0].'="'.$attrSubSet[0].'"';
                }
            }
        }
        return $newSet;
    }

    /**
     * Function to determine if contents of an attribute is safe
     *
     * @param   array   $attrSubSet A 2 element array for attributes name,value
     * @return  boolean True if bad code is detected
     */
    protected function _checkAttribute($attrSubSet)
    {
        $attrSubSet[0] = strtolower($attrSubSet[0]);
        $attrSubSet[1] = strtolower($attrSubSet[1]);
        return (((strpos($attrSubSet[1], 'expression') !== false) && ($attrSubSet[0]) == 'style') || (strpos($attrSubSet[1], 'javascript:') !== false) || (strpos($attrSubSet[1], 'behaviour:') !== false) || (strpos($attrSubSet[1], 'vbscript:') !== false) || (strpos($attrSubSet[1], 'mocha:') !== false) || (strpos($attrSubSet[1], 'livescript:') !== false));
    }

    /**
     * Try to convert to plaintext
     *
     * @param   string  $source
     * @return  string  Plaintext string
     */
    protected function _decode($source)
    {
        // entity decode
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        foreach($trans_tbl as $k => $v) {
            $ttr[$v] = utf8_encode($k);
        }
        $source = strtr($source, $ttr);

        // convert decimal
        $source = preg_replace('/&#(\d+);/me', "chr(\\1)", $source); // decimal notation

        // convert hex
        $source = preg_replace('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $source); // hex notation
        return $source;
    }
}