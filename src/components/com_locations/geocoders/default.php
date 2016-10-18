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
    *   Return a geocoder singleton
    *
    *   @param $config KConfig object
    *
    *   @return ComLocationsGeocoderAdapterAbstract child singleton
    */
    public function getInstance(KConfig $config)
    {
        static $instance;

        if(!is_object($instance)) {
            $service = ucfirst(get_config_value('locations.service', 'google'));
            $class_name = 'ComLocationsGeocoderAdapter'.$service;
            $instance = new $class_name($config);
        }

        return $instance;
    }
}
