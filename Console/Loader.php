<?php 

define('ANAHITA_ROOT', realpath(__DIR__.'/../'));

set_include_path(get_include_path() . PATH_SEPARATOR . ANAHITA_ROOT);

$files = array(
        ANAHITA_ROOT . '/vendor/autoload.php',
        ANAHITA_ROOT . '/../../autoload.php'
);

global $composerLoader, $console;

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

define('WWW_ROOT', COMPOSER_ROOT.'/www');

if ( !file_exists(WWW_ROOT) ) {
    mkdir(WWW_ROOT, 0755);
}
chmod(WWW_ROOT, 0755);
if ( !is_writable(WWW_ROOT) ) {
    print('PHP does not have write access to '.WWW_ROOT.'. Makre sure the permissions are set correctly');
    exit(1);
}
$composerLoader->add('', COMPOSER_ROOT.'/tasks');
$composerLoader->add('Console\\', ANAHITA_ROOT);

//check the tasks folder for any class

$console = new Console\Application();

function include_tasks($directory)
{
    static $_includes_tasks;
    if ( !$_includes_tasks ) {
        $_includes_tasks = array();
    }
    if ( !isset($_includes_tasks[$directory]) )
    {
        if ( is_dir($directory) )
        {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
            $tasks      = array();
            $bootstraps = array();
            foreach($files as $file) {
                if ( strpos($file, '.task.php') ) {
                    $tasks[]  = $file->getPathName();
                }
                elseif ( basename($file) == 'bootstrap.php' ) {
                    $bootstraps[] = $file->getPathName();
                }
            };

            array_walk($bootstraps, function($file) {
                global $console;
                include_once $file;
            });

                array_walk($tasks, function($file) {
                    global $console;
                    include_once $file;
                });
        }
        $_includes_tasks[$directory] = true;
    }
}

include_tasks(ANAHITA_ROOT.'/tasks');
include_tasks(COMPOSER_ROOT.'/tasks');

//include all tasks
foreach($console->getExtensionPackages() as $package) {
    include_tasks($package->getRoot().'/tasks');
}

return $console;
?>