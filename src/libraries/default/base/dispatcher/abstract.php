<?php

/**
 * Abstract Base Dispatcher.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class LibBaseDispatcherAbstract extends LibBaseControllerAbstract
{
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        $this->_controller = $config->controller;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'response' => 'com:base.dispatcher.response',
            'controller' => $this->getIdentifier()->package,
            'request' => array(),
        ));

        parent::_initialize($config);

        //prevent rendering layouts starting with _
        if (strpos($config->request->layout, '_') === 0) {
            unset($config->request->layout);
        }
    }

    /**
     * Method to get a controller object.
     *
     * @return AnControllerAbstract
     */
    public function getController()
    {
        if (! ($this->_controller instanceof LibBaseControllerAbstract)) {

            //Make sure we have a controller identifier
            if (! ($this->_controller instanceof KServiceIdentifier)) {
                $this->setController($this->_controller);
            }

            $config = array(
                'response' => $this->getResponse(),
                'request' => $this->_request,
                'dispatched' => true,
            );

            $this->_controller = $this->getService($this->_controller, $config);
        }

        return $this->_controller;
    }

    /**
     * Set the request of the response.
     *
     * @return LibBaseDispatcherResponse
     */
    public function getResponse()
    {
        if (! $this->_response instanceof LibBaseDispatcherResponse) {
            $this->_response = parent::getResponse();

            if (! $this->_response instanceof LibBaseDispatcherResponse) {
                throw new InvalidArgumentException(
                    'Response: '.get_class($this->_response).' must be an intance of LibBaseDispatcherResponse'
                );
            }

            $this->_response->setRequest($this->getRequest());
        }

        return $this->_response;
    }

    /**
     * Method to set a controller object attached to the dispatcher.
     *
     * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
     * 					or valid identifier string
     *
     * @throws KDispatcherException If the identifier is not a controller identifier
     *
     * @return LibBaseDispatcherAbstract
     */
    public function setController($controller)
    {
        if (! ($controller instanceof LibBaseControllerAbstract)) {
            if (is_string($controller) && strpos($controller, '.') === false) {

                // Controlle name is always singular
                if (AnInflector::isPlural($controller)) {
                    $controller = AnInflector::singularize($controller);
                }

                $identifier = clone $this->getIdentifier();
                $identifier->path = array('controller');
                $identifier->name = $controller;
            } else {
                $identifier = $this->getIdentifier($controller);
            }

            $controller = $identifier;
        }

        $this->_controller = $controller;

        return $this;
    }
}
