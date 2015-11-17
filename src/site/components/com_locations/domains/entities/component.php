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
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'assignable'
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * {@inheritdoc}
     */
    public function onSettingDisplay(KEvent $event)
    {
        $actor = $event->actor;
        $tabs = $event->tabs;
        $tabs->insert('locations', array(
            'label' => JText::_('COM-LOCATIONS-PROFILE-EDIT'),
            'controller' => 'com://site/locations.controller.setting',
        ));
    }

    /**
     * On Dashboard event.
     *
     * @param KEvent $event The event parameter
     */
    public function onDashboardDisplay(KEvent $event)
    {
        $actor = $event->actor;
        $gadgets = $event->gadgets;
        $composers = $event->composers;
        $this->_setGadgets($actor, $gadgets, 'dashboard');
    }

    /**
     * {@inheritdoc}
     */
    protected function _setGadgets($actor, $gadgets, $mode)
    {
        if ($mode == 'dashboard') {
            $gadgets->insert('locations-trending', array(
                'title' => JText::_('COM-LOCATIONS-GADGET-TRENDING'),
                'url' => 'option=com_locations&view=locations&layout=list_gadget&sort=trending&limit=10',
            ));
        }
    }
}
