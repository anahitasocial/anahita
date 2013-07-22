<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
class AnLoaderAdapterComponent extends KLoaderAdapterAbstract
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

			$path     = '/components/'.$component.'/'.$path.'.php';
			$basepath = $this->_basepath;
			
			if ( count($parts) == 2 && $parts[0] == 'domains' )
			{
			    if ( $parts[1] == 'entities' && JPATH_SITE != $this->_basepath )
			    {
			        //set the basepath of entities to the site
			        if ( !file_exists($basepath.$path) ) {			            
			            $basepath = JPATH_SITE;
			        }
			    }
			}			    			

			$path = $basepath.$path;
		}

		return $path;
	}
}

?>