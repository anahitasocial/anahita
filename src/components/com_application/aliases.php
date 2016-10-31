<?php

$settings = KService::get('com:settings.setting');

$cache_prefix = md5($settings->secret).'-cache-system';
$cache_enabled = (extension_loaded('apcu') && ini_get('apc.enabled'));

KService::setAlias('application.registry', 'com:application.registry');
KService::setAlias('application', 'com:application');
KService::setConfig('application.registry', array(
    'cache_prefix' => $cache_prefix,
    'cache_enabled' => $cache_enabled
));
