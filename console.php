<?php 

require_once 'vendor/autoload.php';

require_once 'console/application.php';

$console = new Console\Application(__DIR__,__DIR__.'/site');
$console->run();
exit(0);