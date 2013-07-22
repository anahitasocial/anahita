<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Configuration Controller (Resourceless)
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerConfiguration extends ComBaseControllerResource
{      
    /**
    * Initializes the default configuration for the object
    *
    * Called from {@link __construct()} as a first step of object instantiation.
    *
    * @param KConfig $config An optional KConfig object with configuration options.
    *
    * @return void
    */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'toolbars' => array('menubar')
        ));   

        parent::_initialize($config);
    }
    
    /**
     * Empty action so the before/after browse is invoked
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */ 
    protected function _actionBrowse(KCommandContext $context)
    {             
        //empty
    }
    
    /**
     * Method to set a view object attached to the controller
     *
     * @param mixed $view An object that implements KObjectIdentifiable, an object that 
     * implements KIndentifierInterface or valid identifier string
     *                  
     * @throws KDatabaseRowsetException If the identifier is not a view identifier
     * 
     * @return KControllerAbstract
     */
    public function setView($view)
    {
        parent::setView($view);
        
        if( !($this->_view instanceof LibBaseViewAbstract) ) 
        {
            $defaults[] = 'ComBaseView'.ucfirst($this->view).ucfirst($this->_view->name);
            $defaults[] = 'ComBaseView'.ucfirst($this->_view->name);
            
            //allows to select confiugration view
            register_default(array('identifier'=>$this->_view, 'default'=>$defaults));                        
        }
    }    
}