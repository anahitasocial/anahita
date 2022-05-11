<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Default Actor Component Authorizer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComActorsDomainAuthorizerComponent extends LibBaseDomainAuthorizerDefault
{
    /**
     * Constats on who can add if an actor is not present.
     */
    const CAN_ADD_ADMIN = 0;
    const CAN_ADD_SPECIAL = 1;
    const CAN_ADD_ALL = 2;

    /**
     * Authorize whether we can add a new actor or not.
     * 
     * @param AnCommandContext $context
     * 
     * @return bool
     */
    protected function _authorizeAdd(AnCommandContext $context)
    {
        $can_publish = get_config_value($this->_entity->component, 'can_publish', self::CAN_ADD_ADMIN);

        switch ($can_publish) {
            case self::CAN_ADD_ADMIN :
                return $this->_viewer->admin();
            case self::CAN_ADD_SPECIAL :
                return $this->_viewer->usertype != 'Registered' && !$this->_viewer->guest();
            case self::CAN_ADD_ALL :
                return !$this->_viewer->guest();
            default :
                return false;
        }
    }
}
