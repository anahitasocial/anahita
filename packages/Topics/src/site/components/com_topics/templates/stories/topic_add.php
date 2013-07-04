<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf( @text('COM-TOPICS-STORY-ADD'), @name($subject), @link($object), @possessive($target)); ?>
</data>

<data name="body">
	<?= @helper('text.truncate', @content($object->body, array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
</data>
<?php if ($type == 'notification') :?>
<?php $commands->insert('view-post', array('label'=> @text('COM-TOPICS-TOPIC-VIEW')))->href($object->getURL())?>
<?php endif;?>
