<?php

/**
 * Default permission class
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */

class ComSettingsControllerPermissionDefault extends LibBaseControllerPermissionDefault
{
    public function canExecute($action)
    {
        return get_viewer()->superadmin();
    }

    public function canAdd($action)
    {
        return false;
    }

    public function canDelete($action)
    {
        return false;
    }
}
