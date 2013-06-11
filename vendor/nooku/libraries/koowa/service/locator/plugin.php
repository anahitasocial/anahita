<?php
/**
 * @version 	$Id: plugin.php 4628 2012-05-06 19:56:43Z johanjanssens $
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
class KServiceLocatorPlugin extends KServiceLocatorAbstract
{
    /**
	 * The type
	 *
	 * @var string
	 */
	protected $_type = 'plg';

	/**
	 * Get the classname based on an identifier
	 *
	 * @param  mixed  		 An identifier object - plg.type.plugin.[.path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier)
	{
	    $classpath = KInflector::camelize(implode('_', $identifier->path));
		$classname = 'Plg'.ucfirst($identifier->package).$classpath.ucfirst($identifier->name);

		//Don't allow the auto-loader to load plugin classes if they don't exists yet
		if (!class_exists( $classname)) {
			$classname = false;
		}

		return $classname;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  			An Identifier object - plg.type.plugin.[.path].name
	 * @return string|false		Returns the path on success FALSE on failure
	 */
	public function findPath(KServiceIdentifier $identifier)
	{
	    $path  = '';
	    $parts = $identifier->path;

		$name  = array_shift($parts);
		$type  = $identifier->package;

		if(!empty($identifier->name))
		{
			if(count($parts))
			{
				$path    = array_shift($parts).
				$path   .= count($parts) ? '/'.implode('/', $parts) : '';
				$path   .= DS.strtolower($identifier->name);
			}
			else $path  = strtolower($identifier->name);
		}

		//Plugins can have their own folder
		if (is_file($identifier->basepath.'/plugins/'.$type.'/'.$path.'/'.$path.'.php')) {
		    $path = $identifier->basepath.'/plugins/'.$type.'/'.$path.'/'.$path.'.php';
	    } else {
		    $path = $identifier->basepath.'/plugins/'.$type.'/'.$path.'.php';
		}

		return $path;
	}
}