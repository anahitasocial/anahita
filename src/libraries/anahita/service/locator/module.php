<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
		        $classname = parent::findClass($identifier);		        
		    }
		}
		
		return $classname;
	}
}