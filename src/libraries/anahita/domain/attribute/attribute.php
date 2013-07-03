<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Attribute
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Attribute Factory
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Attribute
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainAttribute 
{	
	/**
	 * Array of classnames
	 * 
	 * @var array
	 */	
	static private $__classnames  = array();
	
	/**
	 * Instances 
	 * 
	 * @var array
	 */
	static private $__instances = array();
	
	/**
	 * If type is one of the aliases type then return the complete type name
	 *
	 * @param string $type The attribute type
	 * 
	 * @return string
	 */	
	static public function getClassname($type)
	{
		if ( !isset(self::$__classnames[$type]) )
		{		    
			$classname = 'AnDomainAttribute'.ucfirst($type);
			$classname = class_exists($classname) ?  $classname : $type;			
			if ( !is($classname, 'AnDomainAttributeInterface') ) 
					throw new AnDomainExceptionType($classname.' must implements AnDomainAttributeInterface');
			self::$__classnames[$type] = $classname;
		}
		
		return self::$__classnames[$type];			
	}
	
	/**
	 * Return the complete type of an composite attribute
	 *
	 * @param string $type The type of the attribute
	 * 
	 * @return  AnDomainAttributeInterface
	 */
	static public function getInstance($type)
	{
		$classname = self::getClassname($type);
			
		if ( !isset(self::$__instances[$classname]) ) 
		{
			$instance = new $classname();
			self::$__instances[$classname] = $instance;
		}
		 
		$instance = clone self::$__instances[$classname];
			
		return $instance;
	}
}