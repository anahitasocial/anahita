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
class AnServiceLocatorComponent extends KServiceLocatorComponent 
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
	 * @param 	mixed  		 An identifier object - koowa:[path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier)
	{
	    $path      = KInflector::camelize(implode('_', $identifier->path));
	        
        $classname = 'Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
        
      	//Manually load the class to set the basepath
		if (!$this->getService('koowa:loader')->loadClass($classname, $identifier->basepath))
		{
		    //the default can be in either in the default folder
		    //be a registered default class
		    $classname = AnServiceClass::findDefaultClass($identifier);
		    
		    if ( !$classname ) 
		    {
		        //let koowa try guess the default class
		        //for the domain objects it won't be able to find 
		        //anything unless
		        $classname = parent::findClass($identifier);
		    }
		    
		    //special case for the domain classes
		    if   ( ($classname === false || $classname == 'AnDomainBehaviorDefault' ) && strpos($path, 'Domain') === 0 ) 
		    {
	            foreach(array('ComBase','LibBase','An') as $prefix)
	            {
	                foreach(array(ucfirst($identifier->name), 'Default') as $name)
	                {
	                    $classname = $prefix.$path.$name;
	                    	                    
	                    if ( $this->getService('koowa:loader')->loadClass($classname, $identifier->basepath) ) {
	                        break;  
	                    }
	                    
	                    $classname = false;
	                }
	                
	                if ( $classname )
	                     break;
	            }
		    }
		}
		
		return $classname;
	}
}