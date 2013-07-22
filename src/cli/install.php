<?php 

require_once('init.php');

$path = @$_SERVER['argv'][1];

$installer = KService::get('com://dev/installer.controller',array(
        'request' => array(
            'path' => $path     
        )
));

/**
 * Installs an extension
 * 
 * php install [path_to_manifest_xml]
 */
print $installer->install();

?>