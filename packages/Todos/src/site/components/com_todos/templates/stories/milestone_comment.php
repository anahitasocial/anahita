<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
    <?= sprintf( @text('COM-TODOS-STORY-NEW-MILESTONE-COMMENT'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">
    <h1 class="entity-title">
    	<?= @link($object)?>
    </h1>
    <div class="entity-body">
	    <?= $comment->body ?>
	</div>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('viewcomment', array('label'=>@text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">
    <h1 class="entity-title">
    	<?= @link($object)?>
    </h1>
    <div class="entity-body">
	    <?= $comment->body ?>
	</div>		
</data>
<?php endif;?>