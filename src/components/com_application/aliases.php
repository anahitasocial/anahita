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

$settings = KService::get('com:settings.setting');
$cache_prefix = md5($settings->secret).'-cache-system';
$cache_enabled = (extension_loaded('apcu') && ini_get('apc.enabled'));

KService::setAlias('application.registry', 'com:application.registry');
KService::setAlias('application', 'com:application');
KService::setConfig('application.registry', array(
    'cache_prefix' => $cache_prefix,
    'cache_enabled' => $cache_enabled
));
