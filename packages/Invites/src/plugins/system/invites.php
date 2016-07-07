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
class plgSystemInvites extends PlgAnahitaDefault
{
    /**
     * onAfterRender handler.
     */
    public function onAfterRoute()
    {
        if(
            KRequest::get('session.invite_token', 'string', null) &&
            KRequest::get('get.option', 'string', null) == 'com_people' &&
            get_viewer()->guest()
        ) {
            $personConfig = KService::get('com://site/settings.template.helper')->getMeta('people');
    		    $personConfig->allowUserRegistration = true;
    		}
    }
}
