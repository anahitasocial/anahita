<?php

/**
 * Default Base Dispatcher.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseDispatcherDefault extends LibBaseDispatcherComponent
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        if ($config->request->getFormat() == 'html' && !$config->request->isAjax()) {
            $this->registerCallback('after.get', array($this, 'includeMedia'));
            $this->registerCallback('after.get', array($this, 'setPageTitle'));
            set_exception_handler(array($this, 'exception'));
        }
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        parent::_initialize($config);

        if ($config->request->view) {
            $config->controller = $config->request->view;
        }
    }

    /**
     * Include Media automatically.
     *
     * This method automatically imports the js/css assets of the app
     *
     * @return mixed
     */
    public function includeMedia()
    {
        $document = $this->getService('anahita:document');
        $asset = $this->getService('com:base.template.asset');

        $url = $asset->getURL("com_{$this->getIdentifier()->package}/js/{$this->getIdentifier()->package}.js");

        if ($url) {
            $document->addScript($url);
        }

        $url = $asset->getURL("com_{$this->getIdentifier()->package}/css/{$this->getIdentifier()->package}.css");

        if ($url) {
            $document->addStyleSheet($url);
        }
    }

    /**
     * Renders a controller view.
     *
     * @return string
     */
    protected function _actionRender(AnCommandContext $context)
    {
        if ($context->request->getFormat() == 'html' && AnRequest::type() == 'HTTP') {
            $this->_setPageTitle();
        }

        return parent::_actionRender($context);
    }

    /**
     * Sets the page title/description.
     *
     * AnCommandContext $context Command Context
     */
    public function setPageTitle(AnCommandContext $context)
    {
        $controller = $this->getController();

        if (! $controller->isIdentifiable()) {
            return;
        }

        $view = $controller->getView();
        $document = $this->getService('anahita:document');

        //@TODO temporary fix
        if ($document->getTitle()) {
            return;
        }

        $item = $controller->getState()->getItem();
        $actorbar = $controller->actorbar;

        $title = array();
        $description = null;

        if ($actorbar && $actorbar->getActor()) {
            if ($actorbar->getTitle()) {
                $title[] = $actorbar->getTitle();
            }

            $description = $actorbar->getDescription();
        } else {
            $title[] = ucfirst($view->getName());
        }

        if ($item && $item->isDescribable()) {
            array_unshift($title, $item->name);
            $description = $item->body;
        }

        $title = implode(' - ', array_filter(array_unique($title)));

        $document->setTitle($title);
        $document->setDescription($description);

        if ($item) {
            $document->setLink(route($item->getURL()));
            if ($item->isPortraitable()) {
                $document->setImage($item->getPortraitURL('large'));
            } else if ($item->isCoverable()) {
                $document->setImage($item->getCoverURL('large'));
            }

            if ($item instanceof ComActorsDomainEntityActor) {
                $document->setType('profile');
            }

            if ($item instanceof ComMediumDomainEntityMedium) {
                $document->setType('article');
            }
        }
    }

    /**
     * Allows the component to handle exception. By default this
     * action passes the exception to the application exception handler.
     *
     * @param AnCommandContext $context Command context
     */
    protected function _actionException(AnCommandContext $context)
    {
        $viewer = $this->getService('com:people.viewer');

        if ($viewer->guest() && $context->data instanceof LibBaseControllerExceptionUnauthorized) {
            throw new AnErrorException(array('You must login first to see this resource!'), AnHttpResponse::UNAUTHORIZED);
            return;
        } else {
            $this->getService('application.dispatcher')->execute('exception', $context);
        }
    }
}
