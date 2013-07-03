<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Service
 * @subpackage Locator
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Anahita Locactor 
 *
 * @category   Anahita
 * @package    Anahita_Service
 * @subpackage Locator
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnServiceLocatorAnahita extends KServiceLocatorAbstract
{
	/** 
	 * The type
	 * 
	 * @var string
	 */
	protected $_type = 'anahita';
	
	/**
	 * Get the classname based on an identifier
	 *
	 * @param 	mixed  		 An identifier object - koowa:[path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier)
	{
	    $classname = 'An'.ucfirst($identifier->package).KInflector::implode($identifier->path).ucfirst($identifier->name);	    
			
		if (!class_exists($classname))
		{
		    $classname = AnServiceClass::findDefaultClass($identifier);
		    
		    if ( !$classname )
		    {
		        // use default class instead
		        $classname = 'An'.ucfirst($identifier->package).KInflector::implode($identifier->path).'Default';
		        
		        if (!class_exists($classname)) {
		            $classname = false;
		        }        
		    }
		}
		
		return $classname;
	}
	
	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	An identifier object - koowa:[path].name
	 * @return string	Returns the path
	 */
	public function findPath(KServiceIdentifier $identifier)
	{
	    $path = '';
	    
	    if(count($identifier->path)) {
			$path .= implode('/',$identifier->path);
		}

		if(!empty($identifier->name)) {
			$path .= '/'.$identifier->name;
		}
				
		$path = $identifier->basepath.'/'.$path.'.php';
		return $path;
	}
}