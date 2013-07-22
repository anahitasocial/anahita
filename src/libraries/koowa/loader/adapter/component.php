<?php
/**
 * @version 	$Id: component.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter for a component
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 * @subpackage 	Adapter
 * @uses		KInflector
 */
class KLoaderAdapterComponent extends KLoaderAdapterAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'com';

	/**
	 * The class prefix
	 *
	 * @var string
	 */
	protected $_prefix = 'Com';

	/**
	 * Get the path based on a class name
	 *
	 * @param  string		  	The class name
	 * @return string|false		Returns the path on success FALSE on failure
	 */
	public function findPath($classname, $basepath = null)
	{
		$path = false;

		$word  = strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $classname));
		$parts = explode(' ', $word);

		if (array_shift($parts) == 'com')
		{
		    //Switch the basepath
		    if(!empty($basepath)) {
		        $this->_basepath = $basepath;
		    }

		    $component = 'com_'.strtolower(array_shift($parts));
			$file 	   = array_pop($parts);

			if(count($parts))
			{
			    if($parts[0] != 'view')
			    {
			        foreach($parts as $key => $value) {
					    $parts[$key] = KInflector::pluralize($value);
				    }
			    }
			    else $parts[0] = KInflector::pluralize($parts[0]);

				$path = implode('/', $parts).'/'.$file;
			}
			else $path = $file;

			$path = $this->_basepath.'/components/'.$component.'/'.$path.'.php';
		}

		return $path;
	}
}