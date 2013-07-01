<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf( @text('COM-TODOS-STORY-NEW-TODO-COMMENT'), @name($subject), @link($object),  @possessive($target)) ?>
</data>

<data name="body">	
	<?= @name($object->author) ?> 
	<?= @helper('text.truncate',  @content($object->body, array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('viewcomment', array('label'=>@text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">	
	<?= $comment->body ?>
</data>
<?php endif;?>





