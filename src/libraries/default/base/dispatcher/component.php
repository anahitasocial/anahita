<?php

/**
 * Component Dispatcher.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       http://www.Anahita.io
 */
class LibBaseDispatcherComponent extends LibBaseDispatcherAbstract implements AnServiceInstantiatable
{
    /**
     * Force creation of a singleton.
     *
     * @param AnConfigInterface  $config    An optional AnConfig object with configuration options
     * @param AnServiceInterface $container A AnServiceInterface object
     *
     * @return AnServiceInstantiatable
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
    {
        if (! $container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);

            //Add the service alias to allow easy access to the singleton
            $container->setAlias('component.dispatcher', $config->service_identifier);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        if ($config->request->has('view')) {
            $this->_controller = $config->request->get('view');
        }
    }

    /**
     * @see KDispatcherAbstract::_actionDispatch()
     */
    protected function _actionDispatch(AnCommandContext $context)
    {
        $identifier = clone $this->getIdentifier();
        $identifier->name = 'aliases';
        $identifier->path = array();

        //Load the component aliases
        $this->getService('anahita:loader')->loadIdentifier($identifier);

        //if a command line the either do get or
        //post depending if there are any action
        if (PHP_SAPI === 'cli') {
            $method = AnRequest::get('post.action', 'cmd', 'get');
        } elseif (file_exists(ANPATH_COMPONENT.'/'.$this->getIdentifier()->package.'.php')) {
            $method = 'renderlegacy';
        } else {
            $method = strtolower(AnRequest::method());
        }
        
        $result = $this->execute($method, $context);
        
        return $result;
    }

    /**
     * Get action.
     *
     * @param AnCommandContext $context
     */
    protected function _actionGet(AnCommandContext $context)
    {
        return $this->getController()->execute('get', $context);
    }

    /**
     * Options action.
     *
     * @param AnCommandContext $context
     */
    protected function _actionOptions(AnCommandContext $context)
    {
        $context->response->status = AnHttpResponse::OK;
        $context->response->set('Content-Length', 0);
    }

    /**
     * Post action.
     *
     * @param AnCommandContext $context
     */
    protected function _actionPost(AnCommandContext $context)
    {
        $context->append(array(
            'data' => AnRequest::get('post', 'raw', array()),
        ));

        //backward compatiblity
        if ($context->data['action']) {
            $context->data['_action'] = $context->data['action'];
        }

        $action = 'post';

        if ($context->data['_action']) {
            $action = $context->data['_action'];
            
            if (in_array($action, array('browse', 'read', 'display'))) {
                throw new LibBaseControllerExceptionMethodNotAllowed('Action: '.$action.' not allowed');
            }
        }

        $this->registerCallback('after.post', array($this, 'forward'));

        return $this->getController()->execute($action, $context);
    }

    /**
     * Get action.
     *
     * @param AnCommandContext $context
     */
    protected function _actionDelete(AnCommandContext $context)
    {
        return $this->getController()->execute('delete', $context);
    }

    /**
     * Renders a controller view.
     *
     * @param AnCommandContext $context The context parameter
     *
     * @return string
     */
    protected function _actionForward(AnCommandContext $context)
    {
        $response = $this->getController()->getResponse();

        if (! $response->getContent()) {
            if (in_array($response->getStatusCode(), array(201, 205))) {
                $view = $this->getController()->getIdentifier()->name;
                $response->setContent($this->getController()->view($view)->execute('display', $context));

                if ($response->getStatusCode() == 205) {
                    $response->setStatus(200);
                }
            }
        }

        return $context->result;
    }
}
