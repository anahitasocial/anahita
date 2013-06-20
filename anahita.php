<?php 

require_once 'vendor/autoload.php';

require_once 'console/application.php';

$console = new Console\Application(__DIR__,__DIR__.'/site');

require_once 'console/commands/create.php';
require_once 'console/commands/bundle.php';
require_once 'console/commands/migrate.php';

$console->run();
exit(0);