<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf(@text('COM-PAGES-STORY-COMMENT'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">	
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= @helper('text.truncate', @escape($object->excerpt), array('length'=>200)); ?>
	</div>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('viewcomment', array('label'=>@text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= $comment->body ?>
	</div>		
</data>
<?php endif;?>

