<?php

/**
 * Converts !location terms to links
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class PlgContentfilterLocation extends PlgContentfilterAbstract
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
            'priority' => KCommand::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

    /**
     * Filter a value.
     *
     * @param string The text to filter
     *
     * @return string
     */
    public function filter($text)
    {
        $text = preg_replace(
            ComLocationsDomainEntityLocation::PATTERN_LOCATION,
            '<a class="location" href="'.JRoute::_('option=com_locations&view=location&alias=$1').'">$0</a>',
            $text);

        return $text;
    }
}
