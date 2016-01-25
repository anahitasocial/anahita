<?php

/**
 * Geocoder Adaptor interface class
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

 interface ComLocationsGeocoderAdapterInterface
 {
    /**
    * Returns the current service name
    *
    * @return string - service identifier
    */
    public function getName();

    /**
    * Returns the service version
    *
    * @return string
    */
    public function getVersion();

    /**
    * Returns the service request status
    *
    * @return string
    */
    public function getStatus();

    /**
    * obtains longitude and latitude values given an address
    *
    * @param string "address, city, state_province, country, zip_postalcode"
    * @return array(long, lat) or else false
    */
    public function geocode($address);
 }
