<?php
/**
 * @version 	$Id: koowa.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter for the Koowa framework
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 * @subpackage 	Adapter
 * @uses 		Koowa
 */
class KLoaderAdapterKoowa extends KLoaderAdapterAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'koowa';

	/**
	 * The class prefix
	 *
	 * @var string
	 */
	protected $_prefix = 'K';

	/**
	 * Get the path based on a class name
	 *
	 * @param  string		  	The class name
	 * @return string|false		Returns the path on success FALSE on failure
	 */
	public function findPath($classname, $basepath = null)
	{
		$path     = false;

		$word  = preg_replace('/(?<=\\w)([A-Z])/', ' \\1',  $classname);
		$parts = explode(' ', $word);

		// If class start with a 'K' it is a Koowa framework class and we handle it
		if(array_shift($parts) == $this->_prefix)
		{
		    $path = strtolower(implode('/', $parts));

			if(count($parts) == 1) {
				$path = $path.'/'.$path;
			}

			if(!is_file($this->_basepath.'/'.$path.'.php')) {
				$path = $path.'/'.strtolower(array_pop($parts));
			}

			$path = $this->_basepath.'/'.$path.'.php';
		}

		return $path;
	}
}