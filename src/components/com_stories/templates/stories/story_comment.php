<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
<?= sprintf(@text('COM-STORIES-TITLE-COMMENT-ON-STORY'), @name($subject), @route($story->object->getURL().'#permalink='.$story->comment->id), @possessive($target))?>
</data>
<data name="body">
<?= $comment->body ?>
</data>
<?php if ($type == 'notification') :?>
<?php $commands->insert('viewcomment', array('label'=>@text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">	
	<?= $comment->body?>
</data>
<?php endif;?>

