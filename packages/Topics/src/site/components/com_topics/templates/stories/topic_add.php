<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf( @text('COM-TOPICS-STORY-ADD'), @name($subject), @route($object->getURL())); ?>
</data>

<data name="body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4> 
    <div class="entity-body">
        <?php $body = @content( $object->body, array('exclude'=>'gist')); ?>
	    <?= @helper('text.truncate', $body, array('length'=>200, 'read_more'=>true, 'consider_html'=>true)); ?>
	</div>
</data>
<?php if ($type == 'notification') :?>
<?php $commands->insert('view-post', array('label'=> @text('COM-TOPICS-TOPIC-VIEW')))->href($object->getURL())?>
<?php endif;?>
