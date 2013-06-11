<?php

PHP_SAPI == 'cli' or die('Access denied');

if ( strpos($_SERVER['SCRIPT_NAME'],'/') === 0 )
    $__FILE__ = $_SERVER['SCRIPT_NAME'];
else
    $__FILE__ = $_SERVER['PWD'].'/'.$_SERVER['SCRIPT_NAME'];
$dir  = realpath(preg_replace('/\/cli.*/','', $__FILE__));

define('JPATH_BASE', $dir.'/administrator');
require_once JPATH_BASE.'/includes/framework.php';

KService::get('com://admin/application.dispatcher')->load();

KServiceIdentifier::setApplication('dev', JPATH_SITE.'/cli');