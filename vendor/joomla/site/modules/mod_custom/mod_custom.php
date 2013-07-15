<?php defined('KOOWA') or die;
dispatch_plugin('content.onPrepareContentModule', array($module, $params), JDispatcher::getInstance());
print $module->content;
?>