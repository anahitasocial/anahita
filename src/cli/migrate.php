<?php 

require_once 'init.php';
require_once 'components/com_migrator/helper.php';

$config = new KConfig(getopt('p:c:'));

$config->append(array(
   'p' =>  @$_SERVER['argv'][1],
   'c' => 'up',
));

$controller = KService::get('com://dev/migrator.controller', array('path'=>$config->p));

print $controller->{$config->c}();

?>