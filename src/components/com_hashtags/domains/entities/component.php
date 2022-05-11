<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */

/**
 * Component object.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComHashtagsDomainEntityComponent extends ComComponentsDomainEntityComponent
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
            $gadgets->insert('hashtags-trending', array(
                'title' => AnTranslator::_('COM-HASHTAGS-GADGET-TRENDING'),
                'url' => 'option=com_hashtags&view=hashtags&layout=list&sort=trending&limit=5',
            ));
        }
    }
}
