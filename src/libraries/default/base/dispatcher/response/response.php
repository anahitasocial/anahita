<?php

 /**
  * Dispatcher Response.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 class LibBaseDispatcherResponse extends LibBaseControllerResponse
 {
     /**
     * Transport object.
     *
     * @var LibBaseDispatcherResponseTransportAbstract
     */
    protected $_transport;

    /**
     * Request object.
     *
     * @var KConfig
     */
    protected $_request;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_transport = $config->transport;
        $this->_request = $config->request;
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
        $config->append(array(
            'transport' => 'default',
            'request' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Return the transport object.
     *
     * @return LibBaseDispatcherResponseTransportAbstract
     */
    public function getTransport()
    {
        if (! $this->_transport instanceof LibBaseDispatcherResponseTransportAbstract) {

            if (! ($this->_transport instanceof KServiceIdentifier)) {
                $this->setTransport($this->_transport);
            }

            $config = array(
                 'response' => $this,
            );

            $this->_transport = $this->getService($this->_transport, $config);
        }

        return $this->_transport;
    }

    /**
     * Set the transport object.
     *
     * @param mixed ${property_name}
     *
     * @return LibBaseDispatcherResponse
     */
    public function setTransport($transport)
    {
        if (! ($transport instanceof LibBaseDispatcherResponseTransportAbstract)) {

            if (is_string($transport) && strpos($transport, '.') === false) {
                $identifier = 'com:base.dispatcher.response.transport.'.$transport;
            } else {
                $identifier = $this->getIdentifier($transport);
            }

            register_default(array(
                'identifier' => $identifier,
                'default' => array('ComBaseDispatcherResponseTransportDefault')
            ));

            $transport = $identifier;
        }

        $this->_transport = $transport;

        return $this;
    }

    /**
     * Return the request.
     *
     * @return KConfig
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set the response request.
     *
     * @param array $request
     */
    public function setRequest($request)
    {
        if (! $request instanceof LibBaseControllerRequest) {
            $request = new LibBaseControllerRequest($request);
        }

        $this->_request = $request;

        return $this;
    }

    /**
     * Sends a header.
     */
    public function send()
    {
        $format = $this->getRequest()->getFormat();
        $this->setTransport($format)->getTransport()->send();
    }
 }
