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
        if(!get_viewer()->superadmin()){
          return false;
        }

        return parent::canExecute($action);
    }

    public function canAdd()
    {
        return false;
    }

    public function canDelete()
    {
        return false;
    }
}
