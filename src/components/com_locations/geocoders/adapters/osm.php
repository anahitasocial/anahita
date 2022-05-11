<?php

/**
 * Geocoder service OpenStreetMap
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComLocationsGeocoderAdapterOsm extends ComLocationsGeocoderAdapterAbstract
{
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
            'name' => 'osm',
            'version' => '0.6',
            'url' => 'https://nominatim.openstreetmap.org/search/',
            'key' => get_config_value('locations.api_key_geocoding', null)
        ));

        parent::_initialize($config);
    }

    /**
    * obtains longitude and latitude values given an address
    *
    * @param string "address, city, state_province, country, zip_postalcode"
    * @return array(long, lat) or else false
    */
    public function geocode($address)
    {
        //visit https://wiki.openstreetmap.org/wiki/Nominatim for implementation

        return false;
    }
}
