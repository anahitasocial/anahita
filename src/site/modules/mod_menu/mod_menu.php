<?php 

print KService::get('mod://site/menu.module', array(
	'request' => $params->toArray()
))->display();