<?php 

define('PATH', array_pop($_SERVER['argv']));

require_once('init.php');

$installer = KService::get('com://dev/installer.controller.default',array(
        'request' => array(
            'path' => PATH     
        )
));

/**
 * Installs an extension
 * 
 * php install [path_to_manifest_xml]
 */
print $installer->install();

?>