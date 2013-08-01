<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Property
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Serializable Interface. An Interface for the properties that are seriliazble and stored in the 
 * database 
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Property
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
interface AnDomainPropertySerializable 
{	
	/**
	 * Serialize a value into database storable values
	 * 
	 * @param mixed $value The value to serialize
	 * 
	 * @return array
	 */
	public function serialize($value);
}

?>