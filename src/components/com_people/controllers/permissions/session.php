<?php

/**
 * Session Permissions.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerPermissionSession extends LibBaseControllerPermissionDefault
{
    /**
     * return true if viewer is a guest.
     *
     * @return bool
     */
    public function canAdd()
    {
        $viewer = $this->getService('com:people.viewer');

        if ($viewer->guest()) {
            return true;
        }

        return false;
    }
}
