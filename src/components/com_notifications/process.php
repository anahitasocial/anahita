<?php

if (!defined('ANPATH_BASE')) {
    $base = dirname($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']);
    $base = str_replace('/components/com_notifications', '', $base);
    define('ANPATH_BASE', $base);
    require_once ANPATH_BASE.'/includes/framework.php';
    KService::get('com://site/application.dispatcher')->load();
}

$settings = KService::get('com:settings.setting');

if ($settings->live_site === '') {
    exit();
}

$scheme = is_ssl() ? 'https://' : 'http://';
$base_url = $scheme.$settings->live_site;

KService::setConfig('com:application.router', array(
    'base_url' => $base_url
));

$controller = KService::get('com:notifications.controller.processor', array('base_url' => $base_url));
$ids = (array) KRequest::get('get.id', 'int', array());

if (!empty($ids)) {
    $controller->id($ids);
}

$controller->process();

exit();
