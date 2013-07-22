<?php 
//print dirname(__FILE__);

function init($base)
{
	define( '_JEXEC', 1 );
	define('JPATH_BASE', $base);
	define( 'DS', DIRECTORY_SEPARATOR );
	
	require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	ini_set('error_reporting', E_ALL);
	jimport('joomla.plugin.helper');
	global $mainframe;
	JFactory::getApplication('site', array('session'=>false));
	$mainframe = JFactory::getApplication();
	JFactory::getConfig()->setValue('cache_handler','');
	JFactory::getConfig()->setValue('session_handler','');
	
	JPluginHelper::importPlugin('notification');
	JFactory::getLanguage()->load('lib_anahita');
	JLoader::import('anahita.anahita', JPATH_LIBRARIES);
	JPluginHelper::importPlugin('system', 'anahita');
	//load the logger plugin
	if ( JDEBUG )
	    JPluginHelper::importPlugin('system', 'logger');
	//LibBaseTemplateAsset::addPath('media');
	//@TODO add the current template to the media path
}

function mail_notification()
{
    $argv = pick(@$_SERVER['argv'], array());
    $ids  = array();
    if ( isset($argv[2]) )
    {
        $ids = explode(',', $argv[2]);
    }
	$query   = KService::get('repos://site/notifications.notification')->getQuery()
						->disableChain()
	                    ->status(ComNotificationsDomainEntityNotification::STATUS_NOT_SENT)
	;
	
    if ( !empty($ids) ) 
        $query->id($ids);
	
	$notifications = $query->fetchSet();

	KService::get('com://site/notifications.mailer')->sendNotifications($notifications);		
}

if ( isset($argv[1]) )
   $base  = $argv[1];
elseif ( $_SERVER['DOCUMENT_ROOT'] )
    $base = dirname($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']);
else
    $base = dirname(__FILE__);

$base = str_replace('/components/com_notifications','',$base);

init($base);
mail_notification();
?>