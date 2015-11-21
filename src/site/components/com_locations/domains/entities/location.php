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
final class ComLocationsDomainEntityLocation extends ComBaseDomainEntityNode
{

  /*
   * location regex pattern
   */
  const PATTERN_LOCATION = '/(?<=\W|^)!([^\d_\s\W][\p{L}\d]{2,})/';

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
                    'read' => 'public',
                    'unique' => true
                ),
                'geoLatitude' => array(
                    'format' => 'float',
                    'read' => 'public'
                ),
                'geoLongitude' => array(
                    'format' => 'float',
                    'read' => 'public'
                ),
                'address' => array(
                    'format' => 'string',
                    'read' => 'public'
                ),
                'city' => array(
                    'format' => 'string',
                    'read' => 'public'
                ),
                'state_province' => array(
                    'format' => 'string',
                    'read' => 'public'
                ),
                'country' => array(
                    'format' => 'string',
                    'read' => 'public'
                ),
                'postalcode' => array(
                    'format' => 'string',
                    'read' => 'public'
                )
            ),
            'behaviors' => to_hash(array(
                'modifiable',
                'describable',
                'authorizer',
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

    /**
    *
    * @return void
    */
    protected function _beforeEntityUpdate()
    {
        $keys = array_keys(KConfig::unbox($this->getModifiedData()));

        if (count(array_intersect($keys, array('city', 'address','postalcode')))){
            KService::get('com://site/locations.geocoder')->geocode($this);
        }
    }

    /**
    *
    * @return void
    */
    /*
    public function updateGeolocations()
    {
        $geocoder = KService::get('com://site/locations.geocoder');
        return $geocoder->geocode($this);


        $city = KInflector::humanize(str_replace('-',' ',$this->city));
        $region = KInflector::humanize(str_replace('-',' ',$this->region));
        $gecode = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($this->address.','.$city.',British Columbia '.$this->postalcode).'&sensor=true';
        $data = json_decode(file_get_contents($gecode),true);

        if ($data && isset($data['results']) && count($data['results']) > 0) {
            $address = $data['results'][0];
            $this->set('geoLatitude', $address['geometry']['location']['lat']);
            $this->set('geoLongitude', $address['geometry']['location']['lng']);
        }
    }
    */
}
