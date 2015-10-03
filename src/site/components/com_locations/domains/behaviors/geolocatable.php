<?php

/**
 * Geolocatable Behavior
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
 class ComLocationsDomainBehaviorGeolocatable extends AnDomainBehaviorAbstract
 {
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
            'relationships' => array(
                'locations' => array(
                    'through' => 'com:locations.domain.entity.tag',
                    'target' => 'com:base.domain.entity.node',
                    'child_key' => 'tagable',
                    'target_child_key' => 'location',
                    'inverse' => true,
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Adds a location to a geolocatable mixer entity
     *
     * @param a word
     */
    public function addGeolocation($name)
    {
        $name = trim($name);

        //@todo implement is_location filter method
        if (!is_string($name)) {
            return;
        }

        if ($location = $this->getService('repos://site/locations.location')->findOrAddNew(array('name' => $name))) {
            $this->locations->insert($location);

            return $this;
        }

        return;
    }

    /**
     * Removes a location from a geolocatable mixer entity
     *
     * @param a word
     */
    public function removeGeolocation($name)
    {
        $name = trim($name);

        //@todo implement is_location filter method
        if (!is_string($name)) {
            return;
        }

        if ($location = $this->getService('repos://site/locations.location')->find(array('name' => $name))) {
            $this->locations->extract($location);

            return $this;
        }

        return;
    }

    /**
     * Change the query to include name
     *
     * Since the target is a simple node. The name field is not included. By ovewriting the
     * tags method we can change the query to include name in the $taggable->tags query
     *
     * @return AnDomainEntitySet
     */
    public function getLocations()
    {
        $this->get('locations')->getQuery()->select('name');

        return $this->get('locations');
    }
 }
