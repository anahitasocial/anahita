<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Template Controller Helper. This helper is used to dispatcher and render another controller
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseTemplateHelperController extends KTemplateHelperAbstract 
{
    /**
     * Static Controller Cache
     *
     * @var array
     */
    static protected $_controllers = array();

    /**
     * Returns a toolbar object of a view
     *
     * @return KControllerToolbarAbstract|null If no toolbar found then null is returned
     */
    public function getToolbar()
    {
        $view    = $this->_template->getView();
        $toolbar = $view->getState()->toolbar;
        if ( !$toolbar )
        {
            $controller = $this->getController($view->getName());
            $toolbar    = $controller->getIdentifier()->name;
            if ( $controller->hasToolbar($toolbar) ) {
                $toolbar = $controller->getToolbar($toolbar);
            }
            $controller->toolbar = $toolbar;
        }
        return $view->getState()->toolbar;
    }
        
	/**
	 * Returns a controller object
	 * 
	 * @param string $name Controller name
	 * 
	 * @return KControllerAbstract
	 */
	public function getController($name)
	{
	    $name = KInflector::singularize($name);
       
	    if ( !isset(self::$_controllers['controller.'.$name]) )
	    {
	        if ( strpos($name,'.') == false )
	        {
	            $identifier = clone $this->getIdentifier();
	            $identifier->name = $name;
	            $identifier->path = array('controller');	            
	        } 
	        else $identifier = $this->getIdentifier($name);
	        
	        $entity = clone $identifier;
	        $entity->path = array('domain','entity');
            
	        try
	        {
	            $repository = AnDomain::getRepository($entity);
	            $entity     = $repository->getClone();
                $default    = array('prefix'=>$entity, 'fallback'=>'ComBaseControllerService'); 	            
	        }
	        catch(Exception $e)
	        {
                $default = array('default'=>'ComBaseControllerResource');
	        }
	        
            $default['identifier'] = $identifier;
            register_default($default);
            	        	        
	        $controller = $this->getService($identifier, array('request' => array()));
	        
	        self::$_controllers['controller.'.$name] = $controller;
	    }
	    
		return self::$_controllers['controller.'.$name];
	}
	
	/**
	 * Returns a controller object
	 * 
	 * @param string $name Controller name
	 * 
	 * @return LibBaseViewAbstract
	 */
	public function getView($name)
	{
	    if ( !isset(self::$_controllers['view.'.$name]) )
	    {
	        $view = $this->getController($name)->setView($name)->getView();
	        self::$_controllers['view.'.$name] = $view;
	    }
	    
	    return self::$_controllers['view.'.$name];	    
	}
}