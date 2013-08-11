<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">		
	<?= sprintf(@text('COM-TOPICS-STORY-COMMENT'), @link($object)) ?>
</data>

<data name="body">	 
	<?= @helper('text.truncate',  @content(strip_tags($object->body), array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('view-comment', array('label'=> @text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">	
	<?= $comment->body ?>
</data>
<?php endif;?>
