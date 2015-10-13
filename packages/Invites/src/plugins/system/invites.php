<?php

jimport('joomla.plugin.plugin');

/**
 * Subscription system plugins. Validates the viewer subscriptions.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class plgSystemInvites extends JPlugin
{
    /**
     * onAfterRender handler.
     */
    public function onAfterRoute()
    {
        global $mainframe;

        if ($mainframe->isAdmin()) {
            return;
        }

        if(
            KRequest::get('session.invite_token', 'string', null) &&
            KRequest::get('get.option', 'string', null) == 'com_people' &&
            get_viewer()->guest()
        ) {
    		    $personConfig = &JComponentHelper::getParams('com_people');
    		    $personConfig->set('allowUserRegistration', true);
    		}
    }
}
