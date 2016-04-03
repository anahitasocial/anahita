<?php

/**
 * Location Permissions
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsControllerPermissionLocation extends ComTagsControllerPermissionDefault
{
    /**
    *  location cannot be added via the controller
    *
    *  @return boolean TRUE if viewer is site admin
    */
    public function canAdd()
    {
       return $this->_viewer->admin();
    }

    /**
    *  location cannot be deleted via the controller
    *
    *  @return boolean
    */
    public function canDelete()
    {
        return $this->getItem()->authorize('delete');
    }
}
