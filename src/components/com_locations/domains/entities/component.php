<?php

/**
 * Locations component entity
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsDomainEntityComponent extends ComComponentsDomainEntityComponent
{
    /**
     * On Dashboard event.
     *
     * @param KEvent $event The event parameter
     */
    public function onDashboardDisplay(KEvent $event)
    {
        $actor = $event->actor;
        $gadgets = $event->gadgets;
        $this->_setGadgets($actor, $gadgets, 'dashboard');
    }

    /**
     * {@inheritdoc}
     */
    protected function _setGadgets($actor, $gadgets, $mode)
    {
        if ($mode == 'dashboard') {
            $gadgets->insert('locations-trending', array(
                'title' => AnTranslator::_('COM-LOCATIONS-GADGET-TRENDING'),
                'url' => 'option=com_locations&view=locations&layout=list_gadget&sort=trending&limit=5',
            ));
        }
    }
}
