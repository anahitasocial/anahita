<?php

/**
 * Geocoder class
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsGeocoderDefault extends KObject
{

    /**
    *   geocoder object ComLocationsGeocoderAdapterAbstract instance
    */
    protected $_geocoder = null;

    /**
    *   Return a geocoder singleton
    *
    *   @param $config KConfig object
    *
    *   @return ComLocationsGeocoderAdapterAbstract child singleton
    */
    public function getInstance(KConfig $config)
    {
        if ($this->_geocoder) {
            return $this->_geocoder;
        }

        $service = ucfirst(get_config_value('locations.service', 'google'));
        $class_name = 'ComLocationsGeocoderAdapter'.$service;
        $this->_geocoder = new $class_name($config);

        return $this->_geocoder;
    }
}
