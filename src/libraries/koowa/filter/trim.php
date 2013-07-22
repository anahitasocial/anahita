<?php
/**
* @version		$Id: trim.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Trim filter.
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterTrim extends KFilterAbstract
{
	/**
     * List of characters provided to the trim() function
     *
     * If this is null, then trim() is called with no specific character list,
     * and its default behavior will be invoked, trimming whitespace.
     *
     * @var string|null
     */
    protected $_charList = null;

    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // List of user-defined tags
        if(isset($config->char_list)) {
            $this->_charList = $config->char_list;
        }
    }

    /**
     * Returns the charList option
     *
     * @return string|null
     */
    public function getCharList()
    {
        return $this->_charList;
    }

    /**
     * Sets the charList option
     *
     * @param  string|null $charList
     * @return this
     */
    public function setCharList($charList)
    {
        $this->_charList = $charList;
        return $this;
    }

    /**
     * Validate a value
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return (is_string($value));
    }

    /**
     * Sanitize a value
     *
     * Returns the variable with characters stripped from the beginning and end
     *
     * @param   mixed   Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        if (null === $this->_charList) {
            return trim((string) $value);
        } else {
            return trim((string) $value, $this->_charList);
        }
    }
}