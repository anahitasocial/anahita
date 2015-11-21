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
class ComLocationsGeocoder extends KObject
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $service = ucfirst(get_config_value('locations.service', 'google'));

        return new ComLocationsGeocoderAdapter.$service($config);
    }
}
