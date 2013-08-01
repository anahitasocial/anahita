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
 * Module Locator. If a module is not found, it first look at the default classes 
 *
 * @category   Anahita
 * @package    Anahita_Service
 * @subpackage Locator
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnServiceLocatorModule extends KServiceLocatorModule
{
	/** 
	 * The type
	 * 
	 * @var string
	 */
	protected $_type = 'mod';
	
	/**
	 * Get the classname based on an identifier
	 *
	 * @param 	mixed  		 An identifier object - koowa:[path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier)
	{
		$path = KInflector::camelize(implode('_', $identifier->path));
	    $classname = 'Mod'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
	    
      	//Manually load the class to set the basepath
		if (!$this->getService('koowa:loader')->loadClass($classname, $identifier->basepath))
		{
		    $classname = AnServiceClass::findDefaultClass($identifier);
		    
		    if ( !$classname ) {
		    	
		    	$classpath = $identifier->path;
		    	$classtype = !empty($classpath) ? array_shift($classpath) : 'view';
		    		
		    	//Create the fallback path and make an exception for views
		    	$com_path = ($classtype != 'view') ? ucfirst($classtype).KInflector::camelize(implode('_', $classpath)) : ucfirst($classtype);
		    	$mod_path = ($classtype != 'view') ? ucfirst($classtype).KInflector::camelize(implode('_', $classpath)) : '';
		    			    	
		    	if(class_exists('Mod'.ucfirst($identifier->package).$mod_path.ucfirst($identifier->name))) {
		    		$classname = 'Mod'.ucfirst($identifier->package).$mod_path.ucfirst($identifier->name);
		    	} elseif(class_exists('Mod'.ucfirst($identifier->package).$mod_path.'Default')) {
		    		$classname = 'Mod'.ucfirst($identifier->package).$mod_path.'Default';
		    	} elseif(class_exists('ModBase'.$mod_path.ucfirst($identifier->name))) {
		    		$classname = 'ModBase'.$mod_path.ucfirst($identifier->name);
		    	} elseif(class_exists('ModBase'.$mod_path.'Default')) {
		    		$classname = 'ModBase'.$mod_path.'Default';
		    	} elseif(class_exists('ComBase'.$com_path.ucfirst($identifier->name))) {
		    		$classname = 'ComBase'.$com_path.ucfirst($identifier->name);
		    	} elseif(class_exists('ComBase'.$com_path.'Default')) {
		    		$classname = 'ComBase'.$com_path.'Default';
	    		} elseif(class_exists('LibBase'.$com_path.ucfirst($identifier->name))) {
	    			$classname = 'LibBase'.$com_path.ucfirst($identifier->name);
	    		} elseif(class_exists('LibBase'.$com_path.'Default')) {
	    			$classname = 'LibBase'.$com_path.'Default';		    				    		
		    	} elseif(class_exists( 'K'.$com_path.ucfirst($identifier->name))) {
		    		$classname = 'K'.$com_path.ucfirst($identifier->name);
		    	} elseif(class_exists('K'.$com_path.'Default')) {
		    		$classname = 'K'.$com_path.'Default';
		    	} else {
		    		$classname = false;
		    	}
		    }
		}
		
		return $classname;
	}
}