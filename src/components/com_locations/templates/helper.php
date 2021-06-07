<?php

/**
 * Location Template Helper
 *
 * Provides helper methods to render geolocation objects
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsTemplateHelper extends LibBaseTemplateHelperAbstract
{
    /**
    *   Method to render a location's address
    */
    public function address(ComLocationsDomainEntityLocation $location)
    {
        $text = array();

        if ($location->address) {
            $text[] = $location->address;
        }

        if ($location->city) {
            $text[] = $location->city;
        }

        if ($location->state_province) {
            $text[] = $location->state_province;
        }

        if ($location->country) {
            $text[] = LibBaseTemplateHelperSelector::$COUNTRIES[$location->country];
        }

        if ($location->postalcode) {
            $text[] = $location->postalcode;
        }

        return implode(', ', $text);
    }
}
