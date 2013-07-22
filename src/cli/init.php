<?php

// Set flag that this is a parent file
define( '_JEXEC', 1 );
if ( !isCli() )
{
    die('Not Allowed');
}
if ( strpos($_SERVER['SCRIPT_NAME'],'/') === 0 )
    $__FILE__ = $_SERVER['SCRIPT_NAME'];
else
    $__FILE__ = $_SERVER['PWD'].'/'.$_SERVER['SCRIPT_NAME'];
$dir      = realpath(preg_replace('/\/cli.*/','', $__FILE__));

define('JPATH_BASE', $dir);
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
jimport('joomla.plugin.helper');
$mainframe =& JFactory::getApplication('site', array('session'=>false));
JFactory::getConfig()->setValue('cache_handler','');
JFactory::getConfig()->setValue('session_handler','');
JPluginHelper::importPlugin('system', 'anahita');
JPluginHelper::importPlugin('notification');
JFactory::getLanguage()->load('lib_anahita');

KServiceIdentifier::setApplication('dev', JPATH_BASE.'/cli');

function devtool_exception_handler($e)
{
    print "\n".'Exception : '.$e->getMessage()."\n\n";   
}

function isCli() {

    if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {
        return true;
    } else {
        return false;
    }
}

set_exception_handler('devtool_exception_handler');
