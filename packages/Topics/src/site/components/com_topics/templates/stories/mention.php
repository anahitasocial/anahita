<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf( @text('COM-TOPICS-STORY-MENTION'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= @helper('text.truncate', @content($object->body, array('exclude'=>'syntax')), array('length'=>200, 'read_more'=>true, 'consider_html'=>true)); ?>
	</div>
</data>

<?php if ($type == 'notification') :?>
<data name="email_body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= $comment->body ?>
	</div>		
</data>
<?php endif;?>