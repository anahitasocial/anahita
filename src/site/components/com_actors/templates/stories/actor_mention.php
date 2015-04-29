<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf( @text('COM-ACTORS-NOTIFICATION-MENTION'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
    	<?php if($object->excerpt): ?>
    	<?= @helper('text.truncate', @escape($object->excerpt), array('length'=>200)); ?>
    	<?php else: ?>
	    <?= @helper('text.truncate', @content(nl2br( $object->body ), array('exclude'=>'gist')), array('length'=>200, 'read_more'=>true, 'consider_html'=>true)); ?>
		<?php endif; ?>
	</div>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('view-post', array('label'=> @text('LIB-AN-MEDIUM-VIEW')))->href($object->getURL())?>
<?php endif;?>