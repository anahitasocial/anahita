<?php
/**
 *
 * @category   Anahita
 * @package    com_application
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2017 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       https://www.GetAnahita.com
 */

$settings = AnService::get('com:settings.config');
$cache_prefix = md5($settings->secret).'-cache-system';
$cache_enabled = (extension_loaded('apcu') && ini_get('apc.enabled'));

AnService::setAlias('application.registry', 'com:application.registry');
AnService::setAlias('application', 'com:application');
AnService::setConfig('application.registry', array(
    'cache_prefix' => $cache_prefix,
    'cache_enabled' => $cache_enabled
));
