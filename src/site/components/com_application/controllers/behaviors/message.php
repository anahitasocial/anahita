<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Application
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Message Behavior
 *
 * @category   Anahita
 * @package    Com_Application
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComApplicationControllerBehaviorMessage extends KControllerBehaviorAbstract
{    
    /**
     * Check if the behavior is enabled or not
     * 
     * @var boolean
     */
    protected $_enabled;
    
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_enabled = $config->enabled;
        $session        = JFactory::getSession();
        $data           = array();
        if ( $this->_enabled ) 
        {   
            $namespace     = $this->_getQueueNamespace(false);
            $data          = (array)$session->get($namespace->queue, new stdClass(), $namespace->namespace);
        }
          
        $config->mixer->getState()->flash = new ComApplicationControllerBehaviorMessageFlash($data);
                
        static $once;
        if ( !$once ) {
            $_SESSION['__controller_persistance'] = array('controller.queue'=>new stdClass());
            $once = true;
        }
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
            'enabled' => KRequest::format() == 'html' && 
                         $config->mixer->isDispatched()
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * If the message is still in the flash, push that to the global 
     * message stack. This gives a chance for the message to be seen
     * 
     * @param KCommandContext $context
     * 
     * @return void
     */
    protected function _afterControllerGet(KCommandContext $context)
    {
        $flash   = $this->_mixer->getState()->flash;
        $message = $flash->getMessage();
        if ( $message ) 
        {
            $message['message'] = JText::_($message['message']);
            $this->storeValue('message', $message, true);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see KControllerBehaviorAbstract::execute()
     */
    public function execute( $name, KCommandContext $context) 
	{
	    return parent::execute($name, $context);
	}
	
    /**
	 * Sets a message
	 * 
	 * @param string  $type    The message type
	 * @param string  $message The message text
	 * @param boolean $global  A flag to whether store the message in the global queue or not
	 * 
	 * @return void
	 */
	public function setMessage($message,$type = 'info', $global = false)
	{
	    //if ajax send back the message 
	    //in the header
	    if ( $this->getRequest()->isAjax() ) {
	        $this->getResponse()->setHeader('X-Message',
	                json_encode(array('text'=>JText::_($message),'type'=>$type)));
	    }
	    else {
	        $this->storeValue('message', array('type'=>$type, 'message'=>$message), $global);
	    }
	}
	
	/**
	 * Stores a value in the session. This value is removed in the next
	 * request
	 * 
	 * @param string  $key    Key to use to store the value
	 * @param string  $value  The value
	 * @param boolean $global Global queue flag 
	 * 
	 * @return void
	 */
	public function storeValue($key, $value, $global = false)
	{
	    if ( $this->_enabled )
	    {
            $namespace   = $this->_getQueueNamespace($global);
            $queue       = JFactory::getSession()
                            ->get($namespace->queue, new stdClass(), $namespace->namespace);
            $queue->$key = $value;
            
            if ( !$global && $this->_mixer->flash ) {
                $this->_mixer->flash->$key = $value;
            }
            
            JFactory::getSession()
                ->set($namespace->queue, $queue, $namespace->namespace);
	    }
	}
	
	/**
	 * Retreive a stored value from the session	 
	 *
	 * @param string $key The value key
	 * @param boolean $global Global queue flag 
	 *  
	 * @return void
	 */
	public function retrieveValue($key, $global = false)
	{
	    $ret = null;
	    
        if ( $this->_enabled )
	    {
            $namespace = $this->_getQueueNamespace($global);
            $queue     = JFactory::getSession()
                            ->get($namespace->queue, new stdClass(), $namespace->namespace);
            $ret       = isset($queue[$key]) ? $queue[$key] : null;
	    }
	    return $ret;    	     
	}

	/**
	 * Return a value queue. If global is set then it returns the global 
	 * queue 
	 * 
	 * @param boolean $global
	 * 
	 * @return array
	 */
	protected function _getQueueNamespace($global = false)
	{
	    $session        = JFactory::getSession();
	     
	    if ( $global ) {
	        $store     = 'application.queue';
	        $namespace = 'default';
	         
	    } else {
	        $store     = (string)$this->_mixer->getIdentifier();
	        $store     = 'controller.queue';
	        $namespace = 'controller_persistance';
	    }
	    
	    return new KConfig(array('queue'=>$store, 'namespace'=>$namespace));
	}
	
    /**
     * Return the object handle
     *
     * @return string
     */
    public function getHandle()
    {
        return KMixinAbstract::getHandle();
    }        
}

