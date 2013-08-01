<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf(@text('COM-PAGES-STORY-COMMENT'), @name($subject), @link($object),  @possessive($target)); ?>
</data>

<data name="body">	
	<?= @name($object->author) ?> 
	<?= @helper('text.truncate', @htmlspecialchars($object->excerpt, ENT_QUOTES), array('length'=>200)); ?>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('viewcomment', array('label'=>@text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">	
	<?= $comment->body ?>
</data>
<?php endif;?>

