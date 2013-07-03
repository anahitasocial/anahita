<?php 


print ComBaseDispatcher::getInstance()
->dispatch( KRequest::get('get.view','cmd','apps') );

?>