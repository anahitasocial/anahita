<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Loader
 * @subpackage Adapter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Loads Anahita related objects 
 *
 * @category   Anahita
 * @package    Anahita_Loader
 * @subpackage Adapter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnLoaderAdapterAnahita extends KLoaderAdapterAbstract
{
	/** 
	 * The adapter type
	 * 
	 * @var string
	 */
	protected $_type = 'anahita';
	
	/**
	 * The class prefix
	 * 
	 * @var string
	 */
	protected $_prefix = 'An';

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
		
		// If class start with a 'An' it is a anahita framework class and we handle it
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

?>