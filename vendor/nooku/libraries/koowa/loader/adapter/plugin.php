<?php
/**
 * @version 	$Id: plugin.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter for a plugin
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 * @subpackage 	Adapter
 * @uses		KInflector
 */
class KLoaderAdapterPlugin extends KLoaderAdapterAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'plg';

	/**
	 * The class prefix
	 *
	 * @var string
	 */
	protected $_prefix = 'Plg';

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

		if (array_shift($parts) == 'plg')
		{
		    //Switch the basepath
		    if(!empty($basepath)) {
		        $this->_basepath = $basepath;
		    }

		    $type = array_shift($parts);

			if(count($parts) > 1) {
				$path = array_shift($parts).'/'.implode('/', $parts);
			} else {
				$path = array_shift($parts);
			}

            //Plugins can have their own folder
		    if (is_file($this->_basepath.'/plugins/'.$type.'/'.$path.'/'.$path.'.php')) {
		        $path = $this->_basepath.'/plugins/'.$type.'/'.$path.'/'.$path.'.php';
			} else {
	            $path = $this->_basepath.'/plugins/'.$type.'/'.$path.'.php';
			}
	    }

		return $path;

	}
}