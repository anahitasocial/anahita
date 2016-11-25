<?php

if (!defined('ANPATH_BASE')) {
    $base = dirname($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']);
    $base = str_replace('/components/com_notifications', '', $base);
    define('ANPATH_BASE', $base);
    require_once ANPATH_BASE.'/includes/framework.php';
    KService::get('com://site/application.dispatcher')->load();
}

$ids = (array) KRequest::get('get.id', 'int', array());
$controller = KService::get('com:notifications.controller.processor');

if (!empty($ids)) {
    $controller->id($ids);
}

$controller->process();

exit();
