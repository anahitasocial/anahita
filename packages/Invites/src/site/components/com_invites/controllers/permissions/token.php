<?php

/**
 * Token permission
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComInvitesControllerPermissionToken extends LibBaseControllerPermissionDefault
{
    /**
    *   Permission to see if token view can be rendered.
    *
    *   @return BOOLEAN true if the passed invitetoken in request is a valid one
    */
    public function canRead()
    {
        $invitetoken = $this->getRequest()->get('invitetoken');
        
        if (
              $token = KService::get('repos:invites.token')->fetch(array('value' => $invitetoken)) &&
              get_viewer()->guest()
        ) {
            return true;
        }

        return false;
    }
}
