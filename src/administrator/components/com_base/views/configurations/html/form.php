<?php defined('KOOWA') or DIE ?>

<form class="-koowa-form" method="post" action="index.php" target="_iframe" >	
	<input type="hidden" name="component" value="<?= $option ?>">
	<input type="hidden" name="controller" value="component">
	<input type="hidden" name="option" value="com_config">
	<input type="hidden" name="task" value="save">
	<input type="hidden" name="<?= JUtility::getToken() ?>" value="1" />	
    <?= @helper('form.render', array(
    	'path'          => JPATH_COMPONENT.DS.'config.xml',
        'element_paths' => array(JPATH_COMPONENT.'/administrator/components/com_base/templates/forms', JPATH_COMPONENT.'/templates/forms'),
    	'data'          => get_config_value($option))) ?>
</form>
<iframe name="_iframe" style="display:none"></iframe>