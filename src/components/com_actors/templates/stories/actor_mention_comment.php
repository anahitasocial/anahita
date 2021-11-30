<?php defined('ANAHITA') or die('Restricted access');?>

<data name="title">		
	<?= sprintf(@text('COM-ACTORS-NOTIFICATION-MENTION-COMMENT'), @name($subject), @route($object->getURL())); ?>
</data>

<data name="body">	 
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= @helper('text.truncate', nl2br($comment->body), array('length' => 200, 'read_more' => true, 'consider_html' => true)); ?>
	</div>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('view-comment', array('label' => @text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= $comment->body ?>
	</div>		
</data>
<?php endif;?>
