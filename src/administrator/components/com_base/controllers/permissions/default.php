<?php

/**
 * Default Permission.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseControllerPermissionDefault extends LibBaseControllerPermissionDefault
{
    /**
     * Generic authorize handler for controller add actions.
     *
     * @return bool Can return both true or false.
     */
    public function canAdd()
    {
        return false;
    }

    /**
     * Generic authorize handler for controller edit actions.
     *
     * @return bool Can return both true or false.
     */
    public function canEdit()
    {
        return get_viewer()->admin();
    }

    /**
     * Generic authorize handler for controller delete actions.
     *
     * @return bool Can return both true or false.
     */
    public function canDelete()
    {
        return false;
    }
}
