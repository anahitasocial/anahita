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
class ComLocationsGeocoderDefault extends AnObject implements AnServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param 	object 	An optional AnConfig object with configuration options
     * @param 	object	A AnServiceInterface object
     * @return AnDatabaseTableInterface
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {

            $service = ucfirst(get_config_value('locations.service', 'google'));
            $classname = 'ComLocationsGeocoderAdapter'.$service;

            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }
}
