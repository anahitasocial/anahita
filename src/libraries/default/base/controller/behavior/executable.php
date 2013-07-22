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
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Executable Controller Behavior
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerBehaviorExecutable extends KControllerBehaviorExecutable
{
    /**
     * The read-only state of the behavior
     *
     * @var boolean
     */
    protected $_readonly;

    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct( KConfig $config)
    {
        parent::__construct($config);

        $this->_readonly = (bool) $config->readonly;
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
        $config->append(array(
             'priority'    => KCommand::PRIORITY_LOW,
             'auto_mixin'  => true,             
        ));
    
        parent::_initialize($config);
    }
        
    /**
     * Command handler
     * 
     * @param string          $name    The command name
     * @param KCommandContext $context The command context
     * 
     * @return boolean     Can return both true or false.  
     */
    public function execute( $name, KCommandContext $context)
    {
        $parts = explode('.', $name);
    
        if($parts[0] == 'before')
        {
            $action = $parts[1];
    
            //Check if the action exists
            if(!in_array($action, $context->caller->getActions()))
            {
                $context->setError(new KControllerException(
                		'Action '.ucfirst($action).' Not Implemented', KHttpResponse::NOT_IMPLEMENTED
                ));
                    
                return false;
            }
                        
            if ( $this->_mixer->canExecute($context) === false ) 
            {
                $context->setError(new KHttpException(
                        'Action '.ucfirst($action).' Not Allowed', KHttpResponse::METHOD_NOT_ALLOWED
                    ));
                
                return false;                    
            }
        }
    
        return true;
    }
    
    /**
     * Set the readonly state of the behavior
     *
     * @param boolean
     * @return KControllerBehaviorExecutable
     */
    public function setReadOnly($readonly)
    {
         $this->_readonly = (bool) $readonly;
         return $this;
    }

    /**
     * Get the readonly state of the behavior
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->_readonly;
    }
        
    /**
    * Authorizes an action. This method can be overriden by the mixer. This method
    * will call can[Action Name]
    *
    * @param KCommandContext $context The CommandChain Context
    *
    * @return boolean
    */
    public function canExecute(KCommandContext $context)
    {
        $action = $context->action;
        
        $ret    = true;
        
        //Check if the action can be executed
        $method = 'can'.ucfirst($action);
        
        if ( in_array($method, $this->_mixer->getMethods()) )
        {
            $ret = $this->_mixer->$method();
        } 
               
        return $ret;
    }
    
    /**
     * Generic authorize handler for controller browse actions
     *
     * @return  boolean     Can return both true or false.
     */
    public function canBrowse()
    {
        return true;
    }

    /**
     * Generic authorize handler for controller read actions
     *
     * @return  boolean     Can return both true or false.
     */
    public function canRead()
    {
        return true;
    }

    /**
     * Generic authorize handler for controller edit actions
     *
     * @return  boolean     Can return both true or false.
     */
    public function canEdit()
    {
        return !$this->_readonly;
    }

    /**
     * Generic authorize handler for controller add actions
     *
     * @return  boolean     Can return both true or false.
     */
    public function canAdd()
    {
        return !$this->_readonly;
    }

    /**
     * Generic authorize handler for controller delete actions
     *
     * @return  boolean     Can return both true or false.
     */
    public function canDelete()
    {
         return !$this->_readonly;
    }          
}