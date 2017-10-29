<?php

/**
 * Invites user plugin.
 *
 * @category   Anahita Invites App
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class plgUserInvites extends PlgAnahitaDefault
{
    /**
     * This method should handle any login logic and report back to the subject.
     *
     * @param   array   holds the user data
     * @param 	array   array holding options (remember, autoregister, group)
     *
     * @return bool True on success
     */
    public function onAfterLoginPerson(KEvent $event)
    {
        KRequest::set('session.invite_token', null);
    }

    /**
     * store user method.
     *
     * Method is called after user data is stored in the database
     *
     * @param 	array		holds the new user data
     * @param 	bool		true if a new user is stored
     * @param	bool		true if user was succesfully stored in the database
     * @param	string		message
     */
    public function onAfterStoreUser(KEvent $event)
    {
        if(!$event->isnew && $event->success){
            KRequest::set('session.invite_token', null);
        }
    }
}
