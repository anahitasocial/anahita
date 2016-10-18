<?php

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
        $token = KRequest::get('session.invite_token', 'string', null);
        $option = KRequest::get('get.option', 'string', null);
        $isGuest = get_viewer()->guest();

        if ($token && $option === 'com_people' && $isGuest) {
            $personConfig = KService::get('com:settings.template.helper')->getMeta('people');
    		$personConfig->allow_registration = true;
    	}
    }
}
