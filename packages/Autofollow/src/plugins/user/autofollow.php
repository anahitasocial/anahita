<?php

/**
 * @version		1.0
 *
 * @category	Anahita Social Plugins
 *
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link     	http://www.GetAnahita.com
 */
jimport('joomla.plugin.plugin');

class plgUserAutoFollow extends JPlugin
{
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
    public function onAfterStoreUser($user, $isnew, $succes, $msg)
    {
        if (!$succes) {
            return false;
        }

        $person = KService::get('repos://site/people.person')
                  ->getQuery()
                  ->disableChain()
                  ->userId($user['id'])
                  ->fetch();

        if ($person) {
            $actor_ids = explode(',', $this->params->get('actor_ids'));

            foreach ($actor_ids as $actor_id) {
                $actor_id = (int) $actor_id;

                if ($actor_id) {
                    $actor = KService::get('repos://site/actors.actor')
                             ->getQuery()
                             ->disableChain()
                             ->fetch($actor_id);

                    if ($actor && $actor->isFollowable()) {
                        $actor->addFollower($person);
                        $actor->save();
                    }
                }
            }
        }
    }
}
