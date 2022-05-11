<?php

/**
 * Default permission class
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.Anahita.io
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
