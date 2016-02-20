<?php

/**
 * Nearby locations query class
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2016 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsDomainQuerySelector extends AnDomainQueryDefault
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
            'repository' => 'repos:locations.location'
        ));

        parent::_initialize($config);
    }

    /**
     * Build the query.
     */
    protected function _beforeQuerySelect()
    {
        if($this->exclude_ids) {
          $this->where('location.id', 'NOT IN', $this->exclude_ids->toArray());
        }

        //nearby
        $nearby_latitude = null;
        $nearby_longitude = null;

        //nearby
        if( $this->locatable->geoLongitude && $this->locatable->geoLatitude ) {

          $nearby_latitude = $this->locatable->geoLatitude;
          $nearby_longitude = $this->locatable->geoLongitude;

        } elseif ($this->nearby_latitude && $this->nearby_longitude) {

            $nearby_latitude = $this->nearby_latitude;
            $nearby_longitude = $this->nearby_longitude;
        }

        //nearby
        if(!$this->keyword && $nearby_latitude && $nearby_longitude) {

          $earth_radius = 6371000;
          $lat = (float) $nearby_latitude;
          $lng = (float) $nearby_longitude;
          $calc_distance = 'CEIL((ACOS(SIN('.$lat.'*PI()/180) * SIN(location.geo_latitude*PI()/180) + COS('.$lat.'*PI()/180) * COS(location.geo_latitude*PI()/180) * COS(('.$lng.'*PI()/180) - (location.geo_longitude*PI()/180) )) *'.$earth_radius.'))';

          $this->select(array($calc_distance.' AS `distance`'));
          $this->having('distance < 5000');
          $this->order('distance', 'ASC');
        }
    }
}
