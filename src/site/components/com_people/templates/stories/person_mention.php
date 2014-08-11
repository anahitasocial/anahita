<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?php if($object extends ComBaseDomainEntityComment) : ?>
	<?= sprintf(@text('LIB-AN--STORY-MENTION-COMMENT'), @name($subject), @route($object->getURL())); ?>
	<?php else: ?>
	<?= sprintf( @text('LIB-AN-MEDIUM-STORY-MENTION'), @name($subject), @route($object->getURL())) ?>
	<?php endif; ?>
</data>

<data name="body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?php if($object->excerpt): ?>
    	<?= @helper('text.truncate', @escape($object->excerpt), array('length'=>200)); ?>
    	<?php else: ?>
	    <?= @helper('text.truncate', @content($object->body, array('exclude'=>'syntax')), array('length'=>200, 'read_more'=>true, 'consider_html'=>true)); ?>
		<?php endif; ?>
	</div>
</data>

<?php if ($type == 'notification') :?>
<?php if($object extends ComBaseDomainEntityComment) : ?>
<?php $commands->insert('view-comment', array('label'=> @text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<?php endif; ?>
<data name="email_body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= $comment->body ?>
	</div>		
</data>
<?php endif;?>