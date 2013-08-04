<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Username filter. It's the same as commadn witout sanitizing that way
 * we can generate DomainError
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleFilterUsername extends KFilterCmd
{
	/**
	 * Validate a value
	 *
	 * @param   scalar  Value to be validated
	 * @return  bool    True when the variable is valid
	 */
	protected function _validate($value)
	{
		$value = trim($value);
		$pattern = '/^[A-Za-z0-9][A-Za-z0-9_-]*$/';
		return (is_string($value) && (preg_match($pattern, $value)) == 1);		
	}	
	
    /**
     * Sanitize a value
     *
     * @param   mixed   Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        //don't allow to sanitize that way we can return an error
        return $value;        
    }
}