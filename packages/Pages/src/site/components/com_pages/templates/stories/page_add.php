<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf(@text('COM-PAGES-STORY-PAGE-ADD'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= @helper('text.truncate', @escape($object->excerpt), array('length'=>200)); ?>
	</div>	
</data>
