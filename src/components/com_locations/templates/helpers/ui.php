<?php

/**
 * Location UI Helper
 *
 * Provides helper methods to render maps
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsTemplateHelperUi extends ComBaseTemplateHelperUi
{
    protected $_service;

    /**
     * Constructor
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        $this->_service = get_config_value('locations.service', 'google');
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
            'paths' => array(dirname(__FILE__).'/ui'),
        ));

        $paths = KConfig::unbox($config->paths);
        array_unshift($paths, ANPATH_THEMES.'/'.$this->getService('application')->getTemplate().'/html/com_locations/ui');
        $config->paths = $paths;

        parent::_initialize($config);
    }

    /**
    * includes location api javascript
    *
    * @param array of configuration params
    *
    * @return string html
    */
    public function api($config = array())
    {
        $config = array_merge_recursive($config, array(
            'key' => get_config_value('locations.browser_key'),
            'service' => $this->_service,
            'libraries' => array()
        ));

        return $this->_render('api_'.$config['service'], $config);
    }

    /**
    * includes places autocomplete javascript
    *
    * @param array of configuration params
    *
    * @return string html
    */
    public function nearby($config = array())
    {
        $config = array_merge_recursive($config, array(
            'service' => $this->_service
        ));

        return $this->_render('nearby', $config);
    }

    /**
    * renders a map
    *
    * @param array of ComLocationsDomainEntityLocation entities
    * @param array of configuration params: longitude, latitude, name, url
    *
    * @return string html
    */
    public function map($locations, $config = array())
    {
        if($locations instanceof ComLocationsDomainEntityLocation){
            $locations = array($locations);
        }

        $data = array();

        foreach($locations as $location){
            $data[] = array(
                'longitude' => $location->longitude,
                'latitude' => $location->latitude,
                'name' => $location->name,
                'url' => route($location->getURL())
            );
        }

        $config['locations'] = htmlspecialchars(json_encode($data), ENT_QUOTES);
        $config['service'] = get_config_value('locations.service', 'google');

        return $this->_render('map_'.$config['service'], $config);
    }

    /**
    * Displays location(s) of a locatable entity or a link to
    * associate the entity to a location
    *
    * @param an entity with geolocatable behaviour
    * @param array of configuration params: longitude, latitude, name, url
    *
    * @return string html
    */
    public function location($entity, $config = array())
    {
        if(!$entity->isGeolocatable()) {
           throw new Exception('Entity is not geolocatable');
        }

        $config['entity'] = $entity;
        $config['locations'] = $entity->locations;

        return $this->_render('location', $config);
    }
}
