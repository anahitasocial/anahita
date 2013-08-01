<?php

/** 
 * LICENSE: ##LICENSE##
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
        
        $this->_action_map['post'] = 'save';
        
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
     * Saves a configuration
     * 
     * @param KCommandContext $context
     * 
     * @return 
     */
    protected function _actionSave(KCommandContext $context)
    {
        $context->append(array('data'=>array('params'=>array())));
        //find or create a new component        
        $component = $this->getService('repos://admin/components.component')
            ->findOrAddNew(array('option'=>'com_'.$this->getIdentifier()->package), 
                    array('data'=>array(
                         'name' => ucfirst($this->getIdentifier()->package)   
                    )));
        $params = new JParameter('');
        $params->loadArray((array)$context->data['params']);
        $component->params = $params->toString();        
        $component->save();
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
        
        if( !($this->_view instanceof LibBaseViewAbstract) ) {
            unregister_default($this->_view);
        }
    }    
}