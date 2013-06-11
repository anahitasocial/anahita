<?php
/**
 * @version 	$Id: component.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Service
 * @subpackage 	Locator
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Locator Adapter for a component
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Service
 * @subpackage 	Locator
 */
class KServiceLocatorComponent extends KServiceLocatorAbstract
{
	/** 
	 * The type
	 * 
	 * @var string
	 */
	protected $_type = 'com';
	
	/**
	 * Get the classname based on an identifier
	 * 
	 * This locator will try to create an generic or default classname on the identifier information
	 * if the actual class cannot be found using a predefined fallback sequence.
	 * 
	 * Fallback sequence : -> Named Component Specific
	 *                     -> Named Component Default  
	 *                     -> Default Component Specific
	 *                     -> Default Component Default
	 *                     -> Framework Specific 
	 *                     -> Framework Default
	 *
	 * @param mixed  		 An identifier object - com:[//application/]component.view.[.path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier)
	{ 
	    $path      = KInflector::camelize(implode('_', $identifier->path));
        $classname = 'Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
        
      	//Manually load the class to set the basepath
		if (!$this->getService('koowa:loader')->loadClass($classname, $identifier->basepath))
		{
		    $classpath = $identifier->path;
			$classtype = !empty($classpath) ? array_shift($classpath) : '';
					
			//Create the fallback path and make an exception for views
			$path = ($classtype != 'view') ? ucfirst($classtype).KInflector::camelize(implode('_', $classpath)) : ucfirst($classtype);
						
			/*
			 * Find the classname to fallback too and auto-load the class
			 * 
			 * Fallback sequence : -> Named Component Specific 
			 *                     -> Named Component Default  
			 *                     -> Default Component Specific 
			 *                     -> Default Component Default
			 *                     -> Framework Specific 
			 *                     -> Framework Default
			 */
			if(class_exists('Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name))) {
				$classname = 'Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
			} elseif(class_exists('Com'.ucfirst($identifier->package).$path.'Default')) {
				$classname = 'Com'.ucfirst($identifier->package).$path.'Default';
			} elseif(class_exists('ComDefault'.$path.ucfirst($identifier->name))) {
				$classname = 'ComDefault'.$path.ucfirst($identifier->name);
			} elseif(class_exists('ComDefault'.$path.'Default')) {
				$classname = 'ComDefault'.$path.'Default';
			} elseif(class_exists( 'K'.$path.ucfirst($identifier->name))) {
				$classname = 'K'.$path.ucfirst($identifier->name);
			} elseif(class_exists('K'.$path.'Default')) {
				$classname = 'K'.$path.'Default';
			} else {
				$classname = false;
			}
		}
		
		return $classname;
	}
	
	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	An identifier object - com:[//application/]component.view.[.path].name
	 * @return string	Returns the path
	 */
	public function findPath(KServiceIdentifier $identifier)
	{
        $path  = '';
	    $parts = $identifier->path;
				
		$component = 'com_'.strtolower($identifier->package);
			
		if(!empty($identifier->name))
		{
			if(count($parts)) 
			{
				if($parts[0] != 'view') 
			    {
			        foreach($parts as $key => $value) {
					    $parts[$key] = KInflector::pluralize($value);
				    }
			    } 
			    else $parts[0] = KInflector::pluralize($parts[0]);
			    
				$path = implode('/', $parts).'/'.strtolower($identifier->name);
			} 
			else $path  = strtolower($identifier->name);	
		}
				
		$path = $identifier->basepath.'/components/'.$component.'/'.$path.'.php';	
		return $path;
	}
}