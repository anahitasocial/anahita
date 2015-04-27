<?php defined('KOOWA') or die ?>

<?php $option = KRequest::get('get.option', 'cmd') ?>

<form class="-koowa-form" method="post" action="index.php?option=<?= $option ?>&view=configurations" >
    <?= @helper('form.render', array(
    	'path'          => JPATH_COMPONENT.DS.'config.xml',
        'element_paths' => array(JPATH_COMPONENT.'/administrator/components/com_base/templates/forms', JPATH_COMPONENT.'/templates/forms'),
    	'data'          => get_config_value($option))) ?>
</form>