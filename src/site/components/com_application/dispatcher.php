<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Application
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
 * @package    Com_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComApplicationDispatcher extends LibApplicationDispatcher 
{
    
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
                 
        //parse route
        $this->registerCallback('before.run',  array($this, 'load'));                       
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
             'application' => 'site'     
        ));
        
        parent::_initialize($config);
    }
  
    /**
     * Run the application dispatcher
     * 
     * @param KCommandContext $context Command chain context
     * 
     * @return boolean
     */
    protected function _actionRun(KCommandContext $context)
    {                
        //initialize the application and load system plugins
        $this->_application->initialise();
                     
        JPluginHelper::importPlugin('system');
        
        $this->_application->triggerEvent('onAfterInitialise');
        
        $this->route();
    }
   
    /**
     * Dispatches the component
     * 
     * @param KCommandContext $context Command chain context
     * 
     * @return boolean
     */        
    protected function _actionDispatch(KCommandContext $context)
    {
        if ( $context->request->get('option') != 'com_application') 
        {
            parent::_actionDispatch($context);
        }        
                
        $this->_application->triggerEvent('onAfterDispatch', array($context));
        
        //render if it's only an HTML
        //otherwise just send back the request
        //@TODO arash. For some reason the line below Need to fix the line below
        //not working properly
        //$redirect = $context->response->isRedirect()
        if ( !$context->response->getHeader('Location') && 
              $context->request->getFormat() == 'html' &&
             !$context->request->isAjax()
                )
        {
            $config = array(
                'request'   => $context->request,
                'response'  => $context->response,
                'theme'     => $this->_application->getTemplate()
            );
            
            $layout = $this->_request->get('tmpl','default');
            
            $this->getService('com://site/application.controller.page', $config)
                ->layout($layout)
                ->render();
        }
        
        $this->_application->triggerEvent('onAfterRender', array($context));
        
        $this->send($context);
    }
    
    /**
     * Routers
     * 
     * @param KCommandContext $context Dispatcher context
     * 
     * @return void
     */
    protected function _actionRoute(KCommandContext $context)
    {
        parent::_actionRoute($context);
        $component = $context->request->get('option');
        if ( empty($component) ) {
            $context->request->set('option', 'com_application'); 
        }
        $this->dispatch();
    }
    
    /**
     * Callback to handle both JError and Exception
     * 
     * @param KCommandContext $context Command chain context
     * caller => KObject, data => mixed
     * 
     * @return KException
     */
    protected function _actionException($context)
    {
        $exception = $context->data;
                
        //if JException then conver it to KException
        if ( $exception instanceof JException ) {
            $exception = new RuntimeException($exception->getMessage(),$exception->getCode());
        }
        
        //if cli just print the error and exit
        if ( PHP_SAPI == 'cli' )
        {
            print "\n";
            print $exception."\n";
            print debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            exit(0);
        }

        $code = $exception->getCode();
        
        //check if the error is code is valid
        if ( $code < 400 || $code >= 600 ) {
            $code = KHttpResponse::INTERNAL_SERVER_ERROR;
        }
                
        $context->response->status = $code; 
        $config = array(
            'response'  => $context->response,
            'request'   => $context->request,
            'theme'     => $this->_application->getTemplate()
        );

        //if ajax or the format is not html
        //then return the exception in json format
        if ( $context->request->isAjax()
             || $context->request->getFormat() != 'html'
                 ) {
            $context->request->setFormat('json');
        }
        
        $this->getService('com://site/application.controller.exception', $config)
                ->layout('error')
                ->render($exception);
                
        $this->send($context);
    }
}