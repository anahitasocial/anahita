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
    public function ______construct(KConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback(array(
          'before.addLocation',
          'before.deleteLocation'
        ), array($this, 'fetchLocation'));
    }

    /**
    *  Method to add a location to a geolocatable node.
    *  If the location node doesn't exist, create it.
    *
    *  @param KCommandContext $context
    *  @return instance of ComBaseDomainEntityNode entity with gelocatable behavior
    */
    protected function _actionAddLocation(KCommandContext $context)
    {
        return $this->getItem()->addLocation($this->location);
    }

    /**
    *  Method to remove a location from a geolocatable node
    *
    *
    *  @param KCommandContext $context
    *  @return instance of ComBaseDomainEntityNode entity with gelocatable behavior
    */
    protected function _actionDeleteLocation(KCommandContext $context)
    {
        return $this->getItem()->deleteLocation($this->location);
    }

    /**
    *   Method to fetch or create a location enitty
    *
    *   @param KCommandContext $context
    *   @return ComLocationsDomainEntityLocation entity
    */
    public function fetchLocation(KCommandContext $context)
    {
        $data = $context->data;

        $data->append(array(
            'id' => $data->location_id
        ));

        $this->location = $this->getService('repos://locations/location')
                               ->getRepository()
                               ->findOrAddNew($data);

        return $this->location;
    }
}
