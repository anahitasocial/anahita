<?php defined('KOOWA') or DIE ?>

<form class="-koowa-form" method="post" action="<?=@route()?>" >
    <?= @helper('form.render', array(
    	'path'          => JPATH_COMPONENT.DS.'config.xml',
        'element_paths' => array(JPATH_COMPONENT.'/administrator/components/com_base/templates/forms', JPATH_COMPONENT.'/templates/forms'),
    	'data'          => get_config_value($option))) ?>
</form>