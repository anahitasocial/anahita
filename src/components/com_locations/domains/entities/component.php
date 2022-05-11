<?php

/**
 * Locations component entity
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComLocationsDomainEntityComponent extends ComComponentsDomainEntityComponent
{
    /**
     * On Dashboard event.
     *
     * @param AnEvent $event The event parameter
     */
    public function onDashboardDisplay(AnEvent $event)
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
