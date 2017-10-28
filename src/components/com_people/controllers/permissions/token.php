<?php

/**
 * Token Permissions.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerPermissionToken extends LibBaseControllerPermissionDefault
{
    /**
     * (non-PHPdoc).
     *
     * @see LibBaseControllerPermissionAbstract::canExecute()
     */
    public function canExecute($action)
    {
        return get_viewer()->guest();
    }
}
