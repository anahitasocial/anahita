<?php

/**
 * Settings Template Helper
 *
 * Provides helper methods to render settins ui objects
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsTemplateHelper extends LibBaseTemplateHelperAbstract
{
  /**
   * Gets the meta object for the app
   *
   * @access public
   * @param string $name the app name
   * @return object app config
   */
  public static function getMeta($name)
  {
      static $instances;

      if (!isset($instances[$name])) {
          $app = KService::get('repos:settings.app')->find(array('package' => 'com_'.$name));
          $instances[$name] = json_decode($app->meta);
      }

      return $instances[$name];
  }
}
