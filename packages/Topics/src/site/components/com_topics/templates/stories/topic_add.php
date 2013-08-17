<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf( @text('COM-TOPICS-STORY-ADD'), @name($subject), @route($object->getURL())); ?>
</data>

<data name="body">
    <h1 class="entity-title">
    	<?= @link($object)?>
    </h1>
    <div class="entity-body">
	    <?= @helper('text.truncate', @content(strip_tags($object->body), array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
	</div>
</data>
<?php if ($type == 'notification') :?>
<?php $commands->insert('view-post', array('label'=> @text('COM-TOPICS-TOPIC-VIEW')))->href($object->getURL())?>
<?php endif;?>
