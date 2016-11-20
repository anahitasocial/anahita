<?php

/**
 * A location node
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
final class ComLocationsDomainEntityLocation extends ComTagsDomainEntityNode
{

  /**
  *  @param geocoder object
  */
  protected $_geocoder = null;

  /**
   * Constructor
   *
   * @param KConfig $config An optional KConfig object with configuration options.
   */
  public function __construct(KConfig $config)
  {
      parent::__construct($config);

      $this->_geocoder = $this->getService('com:locations.geocoder', array('config' => $config));
  }

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
            'attributes' => array(
                'name' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'string',
                    'read' => 'public'
                ),
                'geoLatitude' => array(
                    'format' => 'float',
                    'read' => 'public'
                ),
                'geoLongitude' => array(
                    'format' => 'float',
                    'read' => 'public'
                ),
                'geoAddress' => array(
                    'format' => 'string',
                    'read' => 'public'
                ),
                'geoCity' => array(
                    'format' => 'string',
                    'read' => 'public'
                ),
                'geoState_province' => array(
                    'format' => 'string',
                    'read' => 'public'
                ),
                'geoCountry' => array(
                    'format' => 'string',
                    'read' => 'public'
                ),
                'geoPostalcode' => array(
                    'format' => 'string',
                    'read' => 'public'
                ),
                'enabled' => array('default' => 1)
            ),
            'aliases' => array(
                'latitude' => 'geoLatitude',
                'longitude' => 'geoLongitude',
                'address' => 'geoAddress',
                'city' => 'geoCity',
                'state_province' => 'geoState_province',
                'country' => 'geoCountry',
                'postalcode' => 'geoPostalcode'
            ),
            'behaviors' => to_hash(array(
                'modifiable',
                'describable',
                'authorizer',
                'dictionariable'
            )),
            'relationships' => array(
                'tagables' => array(
                    'through' => 'tag',
                    'child_key' => 'location',
                    'target' => 'com:tags.domain.entity.node',
                    'target_child_key' => 'tagable',
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Update stats.
     */
    public function resetStats(array $locations)
    {
        foreach ($locations as $location) {
            $location->timestamp();
        }
    }

    protected function _beforeEntityInsert()
    {
        $address = implode(',', $this->addressToArray());

        if ($location = $this->_geocoder->geocode($address)) {
            $this->latitude = $location['latitude'];
            $this->longitude = $location['longitude'];
            $this->setValue('results', $location['results']);
        }
    }

    /**
    *
    * @return void
    */
    protected function _beforeEntityUpdate()
    {
        $keys = array_keys(KConfig::unbox($this->getModifiedData()));

        $fields = array(
          'geoAddress',
          'geoCity',
          'geoState_province',
          'geoCountry',
          'geoPostalcode'
        );

        if (count(array_intersect($keys, $fields))){
            $address = $this->addressToArray();
            unset($address['latitude']);
            unset($address['longitude']);
            $address = implode(',', $address);

            if($location = $this->_geocoder->geocode($address)) {
                $this->latitude = $location['latitude'];
                $this->longitude = $location['longitude'];
                $this->setValue('results', $location['results']);
            }
        }
    }

    /**
    *  returns address as an array
    *
    *  @return array
    */
    public function addressToArray()
    {
        return array(
            'address' => $this->address,
            'city' => $this->city,
            'state_province' => $this->state_province,
            'country' => $this->country,
            'postalcode' => $this->postalcode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        );
    }
}
