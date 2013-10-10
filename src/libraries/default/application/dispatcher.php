<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Application Dispatcher
 *
 * @category   Anahita
 * @package    Lib_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibApplicationDispatcher extends LibBaseDispatcherApplication 
{
    /**
     * Application
     *
     * @var JApplication
     */
    protected $_application;
    
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

        $this->_application = $config->application;
        
        if ( PHP_SAPI == 'cli' ) {
            $this->registerCallback('after.load', array($this, 'prepclienv'));
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
            'application' => null
        ));
    
        parent::_initialize($config);
    }     
    
    /**
     * Parses the route
     *
     * @param KCommandContext $context Command chain context
     *
     * @return boolean
     */
    protected function _actionRoute(KCommandContext $context)
    {
        //route the application
        $url  = clone KRequest::url();
        $this->_application->getRouter()->parse($url);
        JRequest::set($url->query, 'get', false);
        // trigger the onAfterRoute events
        $this->_application->triggerEvent('onAfterRoute');
        $url->query = KRequest::get('get','raw');
    
        //globally set ItemId
        global $Itemid;
    
        $Itemid = KRequest::get('get.Itemid','int', 0);
    
        //set the request
        $this->getRequest()->append($url->query);
    
        $component = substr($this->_request->option, 4);
    
        $this->setComponent($component);
    }
        
    /**
     * Loads the application
     *
     * @return void
     */
    protected function _actionLoad($context)
    {
        //already loaded
        if ( $this->_application instanceof JApplication )
            return;
            
//         legacy register error handling
        JError::setErrorHandling( E_ERROR, 'callback', array($this, 'exception'));
    
        //register exception handler
        set_exception_handler(array($this, 'exception'));
    
        $identifier = clone $this->getIdentifier();;
        $identifier->name = 'application';
        
        //load the JSite
        $this->getService('koowa:loader')->loadIdentifier($identifier);
    
        jimport('joomla.application.component.helper');
    
        //no need to create session when using CLI (command line interface)
    
        $this->_application = JFactory::getApplication($this->_application, array('session'=>PHP_SAPI !== 'cli'));
    
        global $mainframe;
    
        $mainframe = $this->_application;
         
        $error_reporting =  $this->_application->getCfg('error_reporting');
    
        define('JDEBUG', $this->_application->getCfg('debug'));
    
        //taken from nooku application dispatcher
        if ($error_reporting > 0)
        {
            error_reporting( $error_reporting );
            ini_set('display_errors',1);
            ini_set('display_startup_errors',1);
        }
    
        $this->getService()->set($identifier, $this->_application);
        $this->getService()->setAlias('application', $identifier);        
    
        //set the session handler to none for
        if ( PHP_SAPI == 'cli' ) {
            JFactory::getConfig()->setValue('config.session_handler','none');
            JFactory::getConfig()->setValue('config.cache_handler','file');
        }
    
        //set the default timezone to UTC
        date_default_timezone_set('UTC');
    
        KRequest::root(str_replace('/'.$this->_application->getName(), '', KRequest::base()));
    }

    /**
     * Prepares the CLI mode
     * 
     * @param KCommandContext $context
     * 
     * @return void
     */
    protected function _actionPrepclienv(KCommandContext $context)
    {
        if ( !empty($_SERVER['argv']) && count($_SERVER['argv']) > 1 ) 
        {
             $args = array_slice($_SERVER['argv'], 1);
             if ( is_readable(realpath($args[0])) ) {
                 $file = array_shift($args);
             }
             $args = explode('&-data&', implode($args,'&'));
             $args = array_filter($args, 'trim');
             foreach($args as $i => $arg) 
             {
                 $arg = trim($arg);
                 if ( $i == 0 ) 
                 {
                    if ( strpos($arg,'/') !== false ) 
                    {
                        $arg  =  substr_replace($arg, '?', strpos($arg,'&'), 1);
                        $url  = KService::get('koowa:http.url', array('url'=>$arg));
                        KRequest::url()->path = KRequest::base().$url->path;
                        $_GET = $url->query;
                    } else {
                        KRequest::url()->path = KRequest::base();
                        parse_str($arg, $_GET);
                    }
                 }
                 
                 else {
                     parse_str($arg, $_POST);
                 }
             }
        }
        
        $_GET['format'] = 'json';
        KRequest::url()->format = 'json';
        KRequest::url()->setQuery($_GET);
        
        jimport('joomla.plugin.helper');
        JPluginHelper::importPlugin('cli');
        $this->_application->triggerEvent('onCli');
        
        //if there's a file then just load the file and exit
        if ( !empty($file) ) 
        {
            KService::get('koowa:loader')->loadFile($file);
            exit(0);
        }
    }
}