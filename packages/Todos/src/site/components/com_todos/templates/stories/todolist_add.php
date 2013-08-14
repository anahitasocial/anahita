<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?=sprintf(@text('COM-TODOS-STORY-NEW-TODO-LIST'), @name($subject))?>
</data>

<data name="body">
	<?= @helper('text.truncate', @content($object->body, array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
</data>

