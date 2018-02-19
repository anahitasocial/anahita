<?php

/**
 * View Controller. This conroller doesn't require domain entities.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseControllerResource extends LibBaseControllerAbstract
{
    /**
     * View object or identifier (APP::com.COMPONENT.view.NAME.FORMAT).
     *
     * @var string|object
     */
    protected $_view;

    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //set the view
        $this->_view = $config->view;

        //register display as get so $this->display() return
        //$this->get()
        $this->registerActionAlias('display', 'get');

        // Mixin the toolbar
        if ($config->dispatch_events) {
            $this->mixin(new KMixinToolbar($config->append(array('mixer' => $this))));
        }

        $this->getService('anahita:language')->load($this->getIdentifier()->package);
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $permission = clone $this->getIdentifier();
        $permission->path = array($permission->path[0], 'permission');
        register_default(array('identifier' => $permission, 'prefix' => $this));

        $config->append(array(
            'behaviors' => array($permission),
            'request' => array('format' => 'html'),
        ))->append(array(
            'view' => $config->request->get ? $config->request->get : ($config->request->view ? $config->request->view : $this->getIdentifier()->name),
        ));

        parent::_initialize($config);
    }

    /**
     * Get action.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return string
     */
    protected function _actionGet(KCommandContext $context)
    {
        $action = null;

        if ($this->_request->get) {
            $action = strtolower('get'.$this->_request->get);
        } else {
            $action = AnInflector::isPlural($this->view) ? 'browse' : 'read';
        }

        $result = null;

        if (in_array($action, $this->getActions())) {
            $result = $this->execute($action, $context);

            if (is_string($result) || $result === false) {
                $context->response->setContent($result.' ');
            }
        }

        $view = $this->getView();

        if (!$context->response->getContent()) {
            if ($context->params) {
                foreach ($context->params as $key => $value) {
                    $view->set($key, $value);
                }
            }

            $content = $view->display();

            //Set the data in the response
            $context->response->setContent($content);
        }

        $context->response->setContentType($view->mimetype);

        return $context->response->getContent();
    }

    /**
     * Get the view object attached to the controller.
     *
     * @return LibBaseViewAbstract
     */
    public function getView()
    {
        if (!$this->_view instanceof LibBaseViewAbstract) {
            //Make sure we have a view identifier
            if (!($this->_view instanceof KServiceIdentifier)) {
                $this->setView($this->_view);
            }

            //Create the view
            $config = array(
                'media_url' => KRequest::root().'/media',
                'base_url' => KRequest::url()->getUrl(KHttpUrl::BASE),
                'state' => $this->getState(),
            );

            if ($this->_request->has('layout')) {
                $config['layout'] = $this->_request->get('layout');
            }

            $this->_view = $this->getService($this->_view, $config);
        }

        return $this->_view;
    }

    /**
     * Method to set a view object attached to the controller.
     *
     * @param mixed $view An object that implements KObjectIdentifiable, an object that
     *                    implements KIndentifierInterface or valid identifier string
     *
     * @throws KDatabaseRowsetException If the identifier is not a view identifier
     *
     * @return AnControllerAbstract
     */
    public function setView($view)
    {
        if (!($view instanceof ComBaseViewAbstract)) {
            if (is_string($view) && strpos($view, '.') === false) {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('view', $view);
                $identifier->name = $this->_request->getFormat();
            } else {
                $identifier = $this->getIdentifier($view);
            }

            register_default(array('identifier' => $identifier, 'prefix' => $this, 'name' => array('View'.ucfirst($identifier->name), 'ViewDefault')));

            $view = $identifier;
        }

        $this->_view = $view;

        return $this;
    }

    /**
     * Set the state property of the controller.
     *
     * @param string $key   The property name
     * @param string $value The property value
     */
    public function __set($key, $value)
    {
        if ($key == 'view') {
            $this->_view = $value;
        }

        //Check for layout, view or format property
        if (in_array($key, array('layout', 'format'))) {
            $this->getRequest()->set($key, $value);

            return $this;
        }

        return parent::__set($key, $value);
    }

    /**
     * Executes a GET request and display the view.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->display();
        } catch (Exception $e) {
            trigger_error('Exception in '.get_class($this).' : '.$e->getMessage(), E_USER_WARNING);
        }
    }

    /**
     * Get a toolbar by identifier.
     *
     * @return AnControllerToolbarAbstract
     */
    public function getToolbar($toolbar, $config = array())
    {
        if (is_string($toolbar)) {
            if (strpos($toolbar, '.') === false) {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('controller', 'toolbar');
                $identifier->name = $toolbar;

                register_default(array('identifier' => $identifier, 'prefix' => $this));

                $toolbar = $identifier;
            }
        }

        return parent::getToolbar($toolbar, $config);
    }
}
