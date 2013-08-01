<?php
if ( !defined('JPATH_BASE') ) 
{
    $base = dirname($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']);
    $base = str_replace('/components/com_notifications','',$base);
    define('JPATH_BASE', $base);
    require_once JPATH_BASE.'/includes/framework.php';
    KService::get('com://site/application.dispatcher')->load(); 
}

class ComNotificationsRouterApplication extends ComApplicationRouter
{
    /**
     * Always return absolute URL
     *
     * (non-PHPdoc)
     * @see ComApplicationRouter::build()
     */
    public function build($query, $fqr = false)
    {
        return parent::build($query, true);
    }
}

KService::setAlias('com://site/application.router',
    'com://site/notifications.router.application');

$base_url = KService::get('koowa:http.url', array('url'=>rtrim(JURI::base(),'/')));

KService::setConfig('com://site/application.router', array(
    'base_url' => $base_url
));

$controller = KService::get('com://site/notifications.controller.processor', array('base_url'=>$base_url));
$ids        = (array)KRequest::get('get.id', 'int', array());
if ( !empty($ids) ) {
    $controller->id($ids);
}
$controller->process();
exit(0);
?>