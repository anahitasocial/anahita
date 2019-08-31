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
   * @param AnConfig $config An optional AnConfig object with configuration options.
   */
  public function __construct(AnConfig $config)
  {
      parent::__construct($config);

      $this->_geocoder = $this->getService('com:locations.geocoder', array('config' => $config));
  }

  /**
   * Initializes the default configuration for the object.
   *
   * Called from {@link __construct()} as a first step of object instantiation.
   *
   * @param AnConfig $config An optional AnConfig object with configuration options.
   */
   protected function _initialize(AnConfig $config)
   {
        $config->append(array(
            'attributes' => array(
                'name' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'string',
                    'length' => array(
                        'max' => 100,
                    ),
                ),
                'body' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'string',
                    'length' => array(
                        'max' => 500,
                    ),
                ),
                'geoLatitude' => array(
                    'required' => true,
                    'format' => 'float',
                    'read' => 'public'
                ),
                'geoLongitude' => array(
                    'required' => true,
                    'format' => 'float',
                    'read' => 'public'
                ),
                'geoAddress' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'string',
                    'read' => 'public',
                    'length' => array(
                        'max' => 100,
                    ),
                ),
                'geoCity' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'string',
                    'read' => 'public',
                    'length' => array(
                        'max' => 100,
                    ),
                ),
                'geoState_province' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'string',
                    'read' => 'public',
                    'length' => array(
                        'max' => 100,
                    ),
                ),
                'geoCountry' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'string',
                    'read' => 'public',
                    'length' => array(
                        'max' => 100,
                    ),
                ),
                'geoPostalcode' => array(
                    'format' => 'string',
                    'read' => 'public',
                    'length' => array(
                        'max' => 10,
                    ),
                ),
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
                'taggables' => array(
                    'through' => 'tag',
                    'child_key' => 'location',
                    'target' => 'com:base.domain.entity.node',
                    'target_child_key' => 'taggable',
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
        $keys = array_keys(AnConfig::unbox($this->getModifiedData()));

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
