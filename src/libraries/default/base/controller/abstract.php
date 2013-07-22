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
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Controller. Nothing different from {@link KControllerAbstract} but override some methods
 * like getBehavior to allow for setting default behavior
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerAbstract extends KControllerAbstract
{    
    /**
     * Controller State
     * 
     * @var KConfigState
     */
    protected $_state;
    
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    public function __construct( KConfig $config)
    {
        parent::__construct($config);                        
    }
              
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
        parent::_initialize($config);    
    }
            
    /**
     * Return the controller state
     * 
     * @return KConfigState
     */   
    public function getState()
    {
        if ( !isset($this->_state) ) {
            $this->_state = new LibBaseControllerState();   
        }
        
        return $this->_state;
    }
     
    /**
     * Set the request information
     *
     * @param array An associative array of request information
     * 
     * @return LibBaseControllerAbstract
     */
    public function setRequest(array $request)
    {
        $this->_request = new KConfig();
        
        foreach($request as $key => $value) {
            $this->_request->$key = $value;
            $this->$key = $value;
        }
        
        return $this;
    }
           
    /**
     * Set the state property of the controller
     * 
     * @param string $key   The property name
     * @param string $value The property value
     * 
     * @return void
     */   
    public function __set($key, $value)
    {
        $this->getState()->$key = $value;
    }
    
    /**
     * Get the state value of a property
     * 
     * @param string $key   The property name
     * 
     * @return void
     */   
    public function __get($key)
    {
        return $this->getState()->$key;
    }
         
    /**
     * Supports a simple form Fluent Interfaces. Allows you to set the request
     * properties by using the request property name as the method name.
     *
     * For example : $controller->view('name')->limit(10)->browse();
     *
     * @param   string  Method name
     * @param   array   Array containing all the arguments for the original call
     * @return  KControllerBread
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        //Handle action alias method
        if(!in_array($method, $this->getActions()) && count($args) )
        {
            //Check first if we are calling a mixed in method.
            //This prevents the model being loaded durig object instantiation.
            if(!isset($this->_mixed_methods[$method]))
            {                
                $this->{KInflector::underscore($method)} = $args[0];            
            
                return $this;
            }
        }
        
        return parent::__call($method, $args);
    }
        
    /**
     * Get a behavior by identifier
     *
     * @param mixed $behavior Behavior name
     * @param array $config   An array of options to configure the behavior with
     *
     * @see KMixinBehavior::getBehavior()
     *
     * @return AnDomainBehaviorAbstract
     */
    public function getBehavior($behavior, $config = array())
    {
        if ( is_string($behavior) )
        {
            if ( strpos($behavior,'.') === false )
            {
                $identifier       = clone $this->getIdentifier();
                $identifier->path = array('controller','behavior');                                     
                $identifier->name = $behavior;
                register_default(array('identifier'=>$identifier, 'prefix'=>$this));                
                $behavior = $identifier;
            }
        }
       
        return parent::__call('getBehavior', array($behavior, $config));
    }
}