<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Dispatcher
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Application Dispatcher
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Dispatcher
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDispatcherApplication extends LibBaseDispatcherAbstract implements KServiceInstantiatable
{   
    /**
     * Force creation of a singleton
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
            $container->setAlias('application.dispatcher', $config->service_identifier);
        }
    
        return $container->get($config->service_identifier);
    }
        
    /**
     * Constructor.
     *
     * @param     object     An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
    
        //Set the component
        $this->setComponent($config->component);
    }
    
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param     object     An optional KConfig object with configuration options.
     * @return     void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'component' => $this->getIdentifier()->package,
        ));
    
        parent::_initialize($config);
    }
        
    /**
     * Method to get a dispatcher object
     *
     * @throws    \UnexpectedValueException    If the controller doesn't implement the ControllerInterface
     * @return    ControllerAbstract
     */
    public function getComponent()
    {
        if(!($this->_controller instanceof LibBaseDispatcherAbstract))
        {
            $this->_controller = $this->getController();

            if(!$this->_controller instanceof LibBaseDispatcherAbstract)
            {
                throw new \UnexpectedValueException(
                    'Dispatcher: '.get_class($this->_controller).' does not implement LibBaseDispatcherAbstract'
                );
            }
        }

        return $this->_controller;
    }

    /**
     * Method to set a dispatcher object
     *
     * @param    mixed    $component  An object that implements ControllerInterface, ServiceIdentifier object
     *                                 or valid identifier string
     * @return    DispatcherAbstract
     */
    public function setComponent($component, $config = array())
    {
        if(!($component instanceof LibBaseDispatcherAbstract))
        {
            if(is_string($component) && 
                    strpos($component, '.') === false )
            {
                $identifier             = clone $this->getIdentifier();
                $identifier->package    = $component;
            }
            else $identifier = $this->getIdentifier($component);                
            $component = $identifier;
        }

        $this->setController($component, $config);

        return $this;
    }

    /**
     * Dispatch the request
     *
     * @param KCommandContext $context    A command context object
     */
    protected function _actionDispatch(KCommandContext $context)
    {        
        $name = 'com_'.$this->getComponent()->getIdentifier()->package;
        
        //legacy
        global $option;
        $option = $name;
        
        define( 'JPATH_COMPONENT',					JPATH_BASE.DS.'components'.DS.$name);
        define( 'JPATH_COMPONENT_SITE',				JPATH_SITE.DS.'components'.DS.$name);
        define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.$name);
        if ( !file_exists(JPATH_COMPONENT) ) {
            throw new LibBaseControllerExceptionNotFound('Component not found');
        }
        if ( !JComponentHelper::isEnabled($name) ) {
            throw new LibBaseControllerExceptionForbidden('Component is disabled');            
        }
        $this->getComponent()->dispatch($context);
    }

    /**
     * Send the response to the client
     *
     * @param CommandContext $context    A command context object
     */
    public function _actionSend(KCommandContext $context)
    {       
        $context->response->send();
        exit(0);
    }
}