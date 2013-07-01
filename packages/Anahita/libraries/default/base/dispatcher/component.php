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
 * Component Dispatcher
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Dispatcher
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDispatcherComponent extends LibBaseDispatcherAbstract implements KServiceInstantiatable
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
            
            //Add the service alias to allow easy access to the singleton
            $container->setAlias('component.dispatcher', $config->service_identifier);            
        }
    
        return $container->get($config->service_identifier);
    }
        
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
        
        if ($config->request->has('view')) {
            $this->_controller = $config->request->get('view');
        }
        
        $this->registerCallback('before.post',   array($this, 'authenticateRequest'));
        $this->registerCallback('before.delete', array($this, 'authenticateRequest'));        
    }
        
    /**
     * @see KDispatcherAbstract::_actionDispatch()
     */
    protected function _actionDispatch(KCommandContext $context)
    {
        $identifier  = clone $this->getIdentifier();
        $identifier->name = 'aliases';
        $identifier->path = array();
        //Load the component aliases
        $this->getService('koowa:loader')->loadIdentifier($identifier);
    
        //if a command line the either do get or
        //post depending if there are any action
        if ( PHP_SAPI == 'cli' ) {
            $method = KRequest::get('post.action', 'cmd', 'get');
        }
    
        elseif ( file_exists(JPATH_COMPONENT.'/'.$this->getIdentifier()->package.'.php') ||
                 file_exists(JPATH_COMPONENT.'/'.'admin.'.$this->getIdentifier()->package.'.php')
                ) {
            $method = 'renderlegacy';
        }
        else 
        {
            $method = strtolower(KRequest::method());
        }
        
        $result = $this->execute($method, $context);
    
        return $result;
    }
    
    /**
     * Get action
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    protected function _actionGet(KCommandContext $context)
    {
        $result = $this->getController()->execute('get', $context);
        return $result;
    }
    
    /**
     * Get action
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    protected function _actionPost(KCommandContext $context)
    {
        $context->append(array(
                'data' => KRequest::get('post', 'raw', array())
        ));
    
        //backward compatiblity
        if ( $context->data['action'] ) {
            $context->data['_action'] = $context->data['action'];
        }
    
        $action        = 'post';
        if ( $context->data['_action'] )
        {
            $action = $context->data['_action'];
            if(in_array($action, array('browse', 'read', 'display'))) {
                throw new LibBaseControllerExceptionMethodNotAllowed('Action: '.$action.' not allowed');
            }
        }
        
        if ( $context->request->getFormat() == 'json' ||
                    $context->request->isAjax() )
        {                
            $this->registerCallback('after.post', array($this, 'forward'));
        }
        else {
            $context->response->setRedirect(KRequest::get('server.HTTP_REFERER', 'url'));            
        }
    
        return $this->getController()->execute($action, $context);
    }
    
    /**
     * Get action
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    protected function _actionDelete(KCommandContext $context)
    {
        //this wil not affect the json calls
        $redirect = KRequest::get('server.HTTP_REFERER', 'url');
    
        $this->getController()
        ->getResponse()
        ->setRedirect($redirect);
    
        $result = $this->getController()->execute('delete', $context);
    
        return $result;
    }

    /**
     * Authenticate a request
     * 
     * @param KCommandContext $context
     * 
     * @return void
     */
    public function authenticateRequest(KCommandContext $context)
    {
        $request = $context->request;
        
        //Check referrer
        if(!$request->getReferrer()) {
            throw new LibBaseControllerExceptionForbidden('Invalid Request Referrer');
        }
    }
    
    /**
     * Renders a controller view
     *
     * @param KCommandContext $context The context parameter
     *
     * @return string
     */
    protected function _actionForward(KCommandContext $context)
    {
        $response = $this->getController()->getResponse();
         
        if ( !$response->getContent() )
        {
            if ( in_array($response->getStatusCode(), array(201, 205)) )
            {
                //set the view to single
                //render the item
                $view   = $this->getController()->getIdentifier()->name;
                $response->setContent($this->getController()->view($view)->execute('display', $context));
                if ( $response->getStatusCode() == 205 ) {
                    $response->setStatus(200);
                }
            }
        }
         
        return $context->result;
    }
        
    /**
     * Renders a legacy component 
     * 
     * @param KCommandContext $context
     */
    protected function _actionRenderlegacy(KCommandContext $context)
    {
        global $option;
        $path = JPATH_COMPONENT.'/'.$this->getIdentifier()->package.'.php';
        if ( !file_exists($path) ) {
            $path = JPATH_COMPONENT.'/'.'admin.'.$this->getIdentifier()->package.'.php';
        }
        $task = JRequest::getString( 'task' );
        ob_start();
        require_once $path;
        $contents = ob_get_contents();
        ob_end_clean();
        $this->getResponse()->setContent($contents);
    }  
}