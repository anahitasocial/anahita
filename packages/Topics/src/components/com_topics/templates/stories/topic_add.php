<? defined('ANAHITA') or die('Restricted access');?>

<data name="title">
	<?= sprintf(@text('COM-TOPICS-STORY-ADD'), @name($subject), @route($object->getURL())); ?>
</data>

<data name="body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= @helper('text.truncate', @body($object->body), array('length' => 200, 'read_more' => true, 'consider_html' => true)); ?>
	</div>
</data>
<? if ($type === 'notification') :?>
<? $commands->insert('view-post', array('label' => @text('COM-TOPICS-TOPIC-VIEW')))->href($object->getURL())?>
<? endif;?>
