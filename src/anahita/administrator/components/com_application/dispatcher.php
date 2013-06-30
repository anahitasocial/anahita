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
        $this->registerCallback('before.run',   array($this, 'load'));      
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
            'application' => 'administrator'                             
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
        jimport('joomla.application.component.helper');
        jimport('joomla.document.renderer');
        require_once(JPATH_LIBRARIES.'/joomla/document/renderer.php');
        require_once(JPATH_BASE.'/includes/toolbar.php');
        
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
        parent::_actionDispatch($context);
        
        $this->_application->triggerEvent('onAfterDispatch', array($context));
        
        //render if it's only an HTML
        //otherwise just send back the request
        if ( !$context->response->isRedirect() &&
              $context->request->getFormat() == 'html' &&
             !$context->request->isAjax()
        )
        {
            $this->render($context);
        } 
        
        $this->_application->triggerEvent('onAfterRender', array($context));
        
        $this->send($context);
    }
        
    /**
     * Renders the output
     * 
     * @param KCommandContext $context Command chain context
     * 
     * @return boolean
     */        
    protected function _actionRender(KCommandContext $context)
    {        
        //old school of rendering for the backend for now        
        $component  = $this->getComponent()->getIdentifier()->package;

        $template   = $this->_application->getTemplate();
        $file       = $this->_request->get('tmpl','index');

        if($component == 'login') {
            $file = 'login';
        }
        
        $config = array(
            'template'  => $template,
            'file'      => $file.'.php',
            'directory' => JPATH_THEMES
        );
        
        $document =& JFactory::getDocument();
        $document->addScript( JURI::root(true).'/administrator/includes/joomla.javascript.js');
        $document->setTitle( htmlspecialchars_decode($this->_application->getCfg('sitename' )). ' - ' .JText::_( 'Administration' ));
        $document->setDescription( $this->_application->getCfg('MetaDesc') );
        
        $document->setBuffer($context->response->getContent(), 'component');
        $content = $document->render(false, $config);
        
        //lets do some parsing. mission template and legacy stuff
        $content = preg_replace_callback('#(src|href)="templates\/#',function($matches){
           return $matches[1].'="'.KRequest::base().'/templates/';
        }, $content);
        
        $content = preg_replace_callback('#(src|href)="/(media|administrator)/#',function($matches){
            return $matches[1].'="'.KRequest::root().'/'.$matches[2].'/';
        }, $content); 
               
        
        $content = preg_replace_callback('#action="index.php"#',function($matches){
            return 'action="'.JRoute::_('index.php?').'"';
        }, $content);
                
        $context->response->setContent($content);       
    }
    
    /**
     * Router action
     * 
     * @param KCommandContext $context
     */
    protected function _actionRoute(KCommandContext $context)
    {     
        //legacy
        if ( KRequest::has('post.option') ) {
            KRequest::set('get.option',KRequest::get('post.option', 'cmd'));
        }
        
        parent::_actionRoute($context);
        
        $component = $this->getRequest()->get('option');
        
        $user =& JFactory::getUser();
        
        if (!$user->authorize('login', 'administrator')) {
            $component = 'com_login';
        }
        
        if( empty($component) ) {
            $component = 'com_cpanel';
        }
        $this->getRequest()->set('option',  $component);
        JRequest::set($this->getRequest()->toArray(),'get');
        $this->setComponent(substr($component, 4));
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
        $error = $context->data;
        if ( $context->response->getHeader('Location') ) 
        {
            $context->response->send();
            exit(0);
        }
        JError::customErrorPage($error);
        exit(0);
    }    
}