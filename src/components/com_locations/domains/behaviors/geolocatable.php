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
    *  Earth's radius in meters
    */
    const EARTH_RADIUS = 6371000;

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
            )
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
                 $this->locations->insert($item);
             }
         }

         return $item;
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

    protected function _beforeQuerySelect(KCommandContext $context)
    {
        $query = $context->query;

        if ($query->search_nearby) {
            $this->_filterDistance($context);
        }
    }

    /**
    * Filter the nodes nearby a particular longitude and latitude
    */
    protected function _filterDistance(KCommandContext $context)
    {
        $query = $context->query;
        $location = $query->search_nearby;

        $lat = $location['latitude'];
        $lng = $location['longitude'];

        //range in kilometers
        $range = $query->search_range * 1000;

        //Spherical Law of Cosines
        $calc_distance = 'CEIL((ACOS(SIN('.$lat.'*PI()/180) * SIN(@col(locations.geo_latitude)*PI()/180) + COS('.$lat.'*PI()/180) * COS(@col(locations.geo_latitude)*PI()/180) * COS(('.$lng.'*PI()/180) - (@col(locations.geo_longitude)*PI()/180) )) *'.self::EARTH_RADIUS.'))';

        $query->select(array($calc_distance.' AS `distance`'));

        $query->group('@col(node.id)');
        $query->having('distance < '.$range);
    }
 }
