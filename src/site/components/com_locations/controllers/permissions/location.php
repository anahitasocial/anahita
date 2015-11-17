<?php

/**
 * Default Tag Permission
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
    *  tags cannot be deleted via the controller
    *
    *  @return boolean ALWAYS FALSE
    */
    public function canDelete()
    {
        return $this->getItem()->authorize('delete');
    }
}
