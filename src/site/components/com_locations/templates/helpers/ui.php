<?php

/**
 * Location UI Helper
 *
 * Provides helper methods to render maps
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsTemplateHelperUi extends ComBaseTemplateHelperUi
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
              'paths' => array(dirname(__FILE__).'/ui'),
        ));

        parent::_initialize($config);

        $paths = KConfig::unbox($config->paths);
        array_unshift($paths, JPATH_THEMES.'/'.JFactory::getApplication()->getTemplate().'/html/com_locations/ui');
        $config->paths = $paths;
    }

    /**
    * renders a map
    *
    * @param array of ComLocationsDomainEntityLocation entities
    * @param array of configurations
    *
    * @return string html
    */
    public function map($locations, $config = array())
    {
        if($locations instanceof ComLocationsDomainEntityLocation){
            $locations = array($locations);
        }

        $data = array();

        foreach($locations as $location){
            $data[] = array(
                'longitude' => $location->geoLongitude,
                'latitude' => $location->geoLatitude,
                'name' => $location->name,
                'url' => $location->getURL()
            );
        }

        $config['locations'] = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
        $config['service'] = get_config_value('locations.service', 'google');

        return $this->_render('map_'.$config['service'], $config);
    }
}
