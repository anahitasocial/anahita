<?php 

//@TODO teribble. should use autoload

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/../');

$files = array(
        __DIR__ . '/../vendor/autoload.php',
        __DIR__ . '/../../../autoload.php'
);

global $composerLoader;

foreach($files as $file) 
{
    if ( file_exists($file) ) 
    {
        $composerLoader = require_once($file);
        define('COMPOSER_VENDOR_DIR', realpath(dirname($file)));
        define('COMPOSER_ROOT', realpath(dirname($file).'/../'));
        break;
    }
}

$composerLoader->add('Console\\', __DIR__.'../console');

$console = new Console\Application(realpath(__DIR__.'/../'), COMPOSER_ROOT.'/www', array('Custom'=>COMPOSER_ROOT.'/packages'),COMPOSER_ROOT.'/config');

require_once 'console/commands/create.php';
require_once 'console/commands/package.php';
require_once 'console/commands/migrate.php';

//include all the tasks
//for now it's good but later we should not do this
//we should use autoload 
if ( file_exists(COMPOSER_ROOT.'/tasks/') ) {
    set_include_path(get_include_path() . PATH_SEPARATOR . COMPOSER_ROOT.'/tasks/');
    $files = new \DirectoryIterator(COMPOSER_ROOT.'/tasks/');
    foreach($files as $file) {
        if ( strpos($file, '.task.php') ) {
            require_once $file->getPathName();
        }
    }
}

$console->run();
exit(0);