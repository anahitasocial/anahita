<?php

/**
 * Geocoder Adaptor abstract class
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
abstract class ComLocationsGeocoderAdapterAbstract extends AnObject implements ComLocationsGeocoderAdapterInterface
{
    /**
    * service name
    *
    * @param STRING
    */
    protected $_name = null;

    /**
    * service API version
    *
    * @param STRING
    */
    protected $_version = null;

    /**
    * service API url
    *
    * @param string
    */
    protected $_url = null;

    /**
    * service API key
    *
    * @param string
    */
    protected $_key = null;

    /**
    * service API request status
    *
    * @param string
    */
    protected $_status = null;

    /**
     * Constructor.
     *
     * @param 	object 	An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_name = $config->name;
        $this->_version = $config->version;
        $this->_url = $config->url;
        $this->_key = $config->key;
        $this->_status = null;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'name' => '',
            'version' => '0.0',
            'url' => '',
            'key' => ''
        ));

        parent::_initialize($config);
    }

    /**
     * Return service name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
    * Returns the service version
    *
    * @return string
    */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
    * Returns the service version
    *
    * @return string
    */
    public function getStatus()
    {
        return $this->_status;
    }
}
