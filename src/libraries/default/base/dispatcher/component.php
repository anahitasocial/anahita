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
 * @link       http://www.GetAnahita.com
 */
class LibBaseDispatcherComponent extends LibBaseDispatcherAbstract implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
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
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if ($config->request->has('view')) {
            $this->_controller = $config->request->get('view');
        }
    }

    /**
     * @see KDispatcherAbstract::_actionDispatch()
     */
    protected function _actionDispatch(KCommandContext $context)
    {
        $identifier = clone $this->getIdentifier();
        $identifier->name = 'aliases';
        $identifier->path = array();

        //Load the component aliases
        $this->getService('koowa:loader')->loadIdentifier($identifier);

        //if a command line the either do get or
        //post depending if there are any action
        if (PHP_SAPI === 'cli') {
            $method = KRequest::get('post.action', 'cmd', 'get');
        } elseif (file_exists(ANPATH_COMPONENT.'/'.$this->getIdentifier()->package.'.php')) {
            $method = 'renderlegacy';
        } else {
            $method = strtolower(KRequest::method());
        }

        $result = $this->execute($method, $context);

        return $result;
    }

    /**
     * Get action.
     *
     * @param KCommandContext $context
     */
    protected function _actionGet(KCommandContext $context)
    {
        return $this->getController()->execute('get', $context);
    }

    /**
     * Options action.
     *
     * @param KCommandContext $context
     */
    protected function _actionOptions(KCommandContext $context)
    {
        $context->response->status = KHttpResponse::OK;
        $context->response->set('Content-Length', 0);
    }

    /**
     * Post action.
     *
     * @param KCommandContext $context
     */
    protected function _actionPost(KCommandContext $context)
    {
        $context->append(array(
            'data' => KRequest::get('post', 'raw', array()),
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

        if ($context->request->getFormat() == 'json' || $context->request->isAjax()) {
            $this->registerCallback('after.post', array($this, 'forward'));
        } else {
            $context->response->setRedirect(KRequest::get('server.HTTP_REFERER', 'url'));
        }

        return $this->getController()->execute($action, $context);
    }

    /**
     * Get action.
     *
     * @param KCommandContext $context
     */
    protected function _actionDelete(KCommandContext $context)
    {
        //this wil not affect the json calls
        $redirect = KRequest::get('server.HTTP_REFERER', 'url');
        $this->getController()->getResponse()->setRedirect($redirect);
        $result = $this->getController()->execute('delete', $context);

        return $result;
    }

    /**
     * Renders a controller view.
     *
     * @param KCommandContext $context The context parameter
     *
     * @return string
     */
    protected function _actionForward(KCommandContext $context)
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
