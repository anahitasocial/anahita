<?php 
/** 
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2020 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.Json>
 *
 * @link       http://www.Anahita.io
 */

class ComSettingsViewAboutJson extends ComBaseViewJson
{
    protected function _getItem()
    {
        return array(
            'title' => 'Anahita Platform & Framework',
            'version' => Anahita::getVersion(),
            'logo' => 'https://s3.ca-central-1.amazonaws.com/production.anahita.io/media/logos/homepage_logo.png',
            'license' => array(
                'name' => 'GPLv3',
                'url' => 'https://www.gnu.org/licenses/gpl-3.0.en.html',
            ),
            'website' => array(
                'name' => 'Anahita.io',
                'url' => 'https://www.anahita.io',
            )
        );
    }
}