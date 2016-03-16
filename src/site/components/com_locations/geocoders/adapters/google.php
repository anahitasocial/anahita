<?php

/**
 * Geocoder service Google
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsGeocoderAdapterGoogle extends ComLocationsGeocoderAdapterAbstract
{
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
            'name' => 'google',
            'version' => '3',
            'url' => 'https://maps.googleapis.com/maps/api/geocode/json?',
            'key' => get_config_value('locations.server_key', null)
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
        $gecode = $this->_url.'address='.urlencode($address);

        if($this->_key){
            $gecode .= '&key='.$this->_key;
        }

        $data = json_decode(file_get_contents($gecode),true);

        $this->_status = $data['status'];

        if ($this->_status === 'OK') {
            $results = $data['results'][0]['geometry']['location'];
            $location = array(
                'longitude' => $results['lng'],
                'latitude' => $results['lat'],
                'results' => $data['results'],
            );

            return $location;
        } else {
            return false;
        }
    }
}
