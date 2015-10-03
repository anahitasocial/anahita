<?php

/**
 * Geolocatable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsControllerBehaviorGeolocatable extends KControllerBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback('after.add', array($this, 'addLocationsFromBody'));
        $this->registerCallback('after.edit', array($this, 'updateLocationsFromBody'));
    }

    /**
     *  Extracts location names from the entity body and add them to the item
     *
     *  @return void
     */
    public function addLocationsFromBody()
    {
        $entity = $this->getItem();
        $names = $this->extractLocationNames($entity->body);

        foreach ($names as $name) {
            $entity->addLocation(trim($name));
        }

        return;
    }

    /**
     * Extracts locations from the entity body and updates the entity.
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    public function updateLocationsFromBody(KCommandContext $context)
    {
        $entity = $this->getItem();
        $names = $this->extractLocationNames($entity->body);

        if (is_array($names)) {
            $names_search = array_map('strtolower', $names);

            foreach ($entity->locations as $location) {
                if (!in_array($location->name, $names_search)) {
                    $entity->removeLocation($location->name);
                }
            }
        }

        foreach ($names as $name) {
            $entity->addLocation(trim($name));
        }
    }

    /**
     * extracts a list of location names from a given text.
     *
     * @return array
     */
    public function extractLocationNames($text)
    {
        $matches = array();

        if (preg_match_all(ComLocationsDomainEntityLocation::PATTERN_LOCATION, $text, $matches)) {
            return array_unique($matches[1]);
        } else {
            return array();
        }
    }

    /**
     * Applies the location filtering to the browse query.
     *
     * @param KCommandContext $context
     */
    protected function _beforeControllerBrowse(KCommandContext $context)
    {
        if (!$context->query) {
            $context->query = $this->_mixer->getRepository()->getQuery();
        }

        if ($this->location) {
            $query = $context->query;
            $locations = array();
            $entityType = KInflector::singularize($this->_mixer->getIdentifier()->name);
            $this->location = (is_string($this->location)) ? array($this->location) : $this->location;

            $edgeType = 'ComTagsDomainEntityTag,ComLocationsDomainEntityTag,com:locations.domain.entity.tag';

            $query
            ->join('left', 'edges AS location_edge', '('.$entityType.'.id = location_edge.node_b_id AND location_edge.type=\''.$edgeType.'\')')
            ->join('left', 'nodes AS location', 'location_edge.node_a_id = location.id');

            foreach ($this->location as $location) {
                $location = $this->getService('com://site/locations.filter.location')->sanitize($location);
                if ($location != '') {
                    $locations[] = $location;
                }
            }

            $query
            ->where('location.name', 'IN', $locations)
            ->group($entityType.'.id');

            //print str_replace('#_', 'jos', $query);
        }
    }
}
