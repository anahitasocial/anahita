<?php

/**
 * System settings Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */

class ComSettingsControllerSetting extends ComBaseControllerResource
{
    /**
    *   browse service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionRead(KCommandContext $context)
    {
        $setting = new JConfig();
        $this->getView()->set('setting', $setting);
    }

    /**
    *   edit service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionEdit(KCommandContext $context)
    {

    }
}
