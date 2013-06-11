<?php
/**
* @version		$Id: slug.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Slug filter
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterSlug extends KFilterAbstract
{
	/**
	 * Separator character / string to use for replacing non alphabetic characters
	 * in generated slug
	 *
	 * @var	string
	 */
	protected $_separator;

	/**
	 * Maximum length the generated slug can have. If this is null the length of
	 * the slug column will be used.
	 *
	 * @var	integer
	 */
	protected $_length;

	/**
	 * Constructor
	 *
	 * @param 	object	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_length    = $config->length;
		$this->_separator = $config->separator;
	}

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @return void
     */
	protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'separator' => '-',
    		'length' 	=> 100
	  	));

    	parent::_initialize($config);
   	}

	/**
	 * Validate a value
	 *
	 * Returns true if the string only contains US-ASCII and does not contain
	 * any spaces
	 *
	 * @param	mixed	Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		return $this->getService('koowa:filter.cmd')->validate($value);
	}

	/**
	 * Sanitize a value
	 *
	 * Replace all accented UTF-8 characters by unaccented ASCII-7 "equivalents",
	 * replace whitespaces by hyphens and lowercase the result.
	 *
	 * @param	scalar	Variable to be sanitized
	 * @return	scalar
	 */
	protected function _sanitize($value)
	{
		//remove any '-' from the string they will be used as concatonater
		$value = str_replace($this->_separator, ' ', $value);

		//convert to ascii characters
		$value = $this->getService('koowa:filter.ascii')->sanitize($value);

		//lowercase and trim
		$value = trim(strtolower($value));

		//remove any duplicate whitespace, and ensure all characters are alphanumeric
		$value = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array($this->_separator,''), $value);

		//remove repeated occurences of the separator
		$value = preg_replace('/['.preg_quote($this->_separator, '/').']+/', $this->_separator, $value);

		//limit length
		if (strlen($value) > $this->_length) {
			$value = substr($value, 0, $this->_length);
		}

		return $value;
	}
}