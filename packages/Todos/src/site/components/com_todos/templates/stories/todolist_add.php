<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?=sprintf(@text('COM-TODOS-STORY-NEW-TODO-LIST'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">
	<h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= @helper('text.truncate', @content(strip_tags($object->body), array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
	</div>
</data>

