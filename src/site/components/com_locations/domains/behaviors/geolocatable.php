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
     * Set locations to a locatable node
     *
     * @param entity set of location entities
     */
     public function editLocations($locations)
     {
         $new_ids = (array) KConfig::unbox($locations->id);

         foreach ($this->locations as $location) {
             if (!in_array($location->id, $new_ids)) {
                 if ($edge = $this->locations->find($location)) {
                     $edge->delete();
                 }
             }
         }

         $newItems = AnHelperArray::getIterator($locations);

         foreach ($newItems as $item) {
             if (! $this->locations->find($item)) {
                 $this->locations->insert( $item );
             }
         }

         return $this;
     }

     /**
     * add locations to a locatable node
     *
     * @param entity set of location entities
     */
     public function addLocation($locations)
     {
         $newItems = AnHelperArray::getIterator($locations);

         foreach ($newItems as $item) {
             if (! $this->locations->find($item)) {
                 $this->locations->insert( $item );
             }
         }

         return $this;
     }

     /**
     * Set locations to a locatable node
     *
     * @param entity set of location entities
     */
     public function deleteLocation($locations)
     {
         $delete_ids = (array) KConfig::unbox($locations->id);

         foreach ($this->locations as $location) {
             if (in_array($location->id, $delete_ids)) {
                 if ($edge = $this->locations->find($location)) {
                     $edge->delete();
                 }
             }
         }

         return $this;
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

    /**
    * Filter the nodes nearby a particular longitude and latitude
    */
    protected function _beforeRepositoryFetch(KCommandContext $context)
    {
        $query = $context->query;

        if ($query->search_nearby && false) {

            $location = $query->search_nearby;
            $lat = $location['latitude'];
            $lng = $location['longitude'];

            $range = 10 * 1000;

            $lng_b1 = $lng - ($range / 111302.62);
            $lng_b2 = $lng + ($range / 111302.62);

            $lat_b1 = $lat - ($range / 110574.61);
            $lat_b2 = $lat + ($range / 110574.61);

            $query->where("(@col(locations.geo_latitude) BETWEEN $lat_b1 AND $lat_b2) AND (@col(locations.geo_longitude) BETWEEN $lng_b1 AND $lng_b2)");

            $query->select('GROUP_CONCAT(@col(locations.id)) AS location_ids');

            $query->group('@col(id)');
        }

        print str_replace('#_', 'jos', $query);
    }
 }
