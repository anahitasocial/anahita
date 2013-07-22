<?php
/**
 * @version 	$Id: module.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Service
 * @subpackage 	Locator
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Service Locator for a plugin
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Service
 * @subpackage 	Locator
 */
class KServiceLocatorModule extends KServiceLocatorAbstract
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
	 * This locator will try to create an generic or default classname on the identifier information
	 * if the actual class cannot be found using a predefined fallback sequence.
	 * 
	 * Fallback sequence : -> Named Module Specific
	 *                     -> Named Module Default  
	 *                     -> Default Module Specific
	 *                     -> Default Module Default
	 *                     -> Framework Specific 
	 *                     -> Framework Default
	 *
	 * @param mixed  		 An identifier object - mod:[//application/]module.[.path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier)
	{		
	    $path = KInflector::camelize(implode('_', $identifier->path));
		$classname = 'Mod'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
			
		//Don't allow the auto-loader to load module classes if they don't exists yet
		if (!$this->getService('koowa:loader')->loadClass($classname, $identifier->basepath))
		{
			$classpath = $identifier->path;
			$classtype = !empty($classpath) ? array_shift($classpath) : 'view';
			
			//Create the fallback path and make an exception for views
			$com_path = ($classtype != 'view') ? ucfirst($classtype).KInflector::camelize(implode('_', $classpath)) : ucfirst($classtype);
			$mod_path = ($classtype != 'view') ? ucfirst($classtype).KInflector::camelize(implode('_', $classpath)) : '';
				
			/*
			 * Find the classname to fallback too and auto-load the class
			 * 
			 * Fallback sequence : -> Named Module Specific 
			 *                     -> Named Module Default  
			 *                     -> Default Module Specific 
			 *                     -> Default Module Default
			 *                     -> Default Component Specific 
			 *                     -> Default Component Default
			 *                     -> Framework Specific 
			 *                     -> Framework Default
			 */
			if(class_exists('Mod'.ucfirst($identifier->package).$mod_path.ucfirst($identifier->name))) {
				$classname = 'Mod'.ucfirst($identifier->package).$mod_path.ucfirst($identifier->name);
			} elseif(class_exists('Mod'.ucfirst($identifier->package).$mod_path.'Default')) {
				$classname = 'Mod'.ucfirst($identifier->package).$mod_path.'Default';
			} elseif(class_exists('ModDefault'.$mod_path.ucfirst($identifier->name))) {
				$classname = 'ModDefault'.$mod_path.ucfirst($identifier->name);
			} elseif(class_exists('ModDefault'.$mod_path.'Default')) {
				$classname = 'ModDefault'.$mod_path.'Default';
			} elseif(class_exists('ComDefault'.$com_path.ucfirst($identifier->name))) {
				$classname = 'ComDefault'.$com_path.ucfirst($identifier->name);
			} elseif(class_exists('ComDefault'.$com_path.'Default')) {
				$classname = 'ComDefault'.$com_path.'Default';
			} elseif(class_exists( 'K'.$com_path.ucfirst($identifier->name))) {
				$classname = 'K'.$com_path.ucfirst($identifier->name);
			} elseif(class_exists('K'.$com_path.'Default')) {
				$classname = 'K'.$com_path.'Default';
			} else {
				$classname = false;
			}
		
		}
	    
		return $classname;
	}
	
	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	An identifier object - mod:[//application/]module.[.path].name
	 * @return string	Returns the path
	 */
	public function findPath(KServiceIdentifier $identifier)
	{
		$path  = '';
	    $parts = $identifier->path;
		$name  = $identifier->package;
				
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
				
		$path = $identifier->basepath.'/modules/mod_'.$name.'/'.$path.'.php';			
	    return $path;
	}
}