<?php defined('KOOWA') or die('Restricted access');?>

<?php if ( is_array($object) ) : ?>
<data name="title">	
	<?= sprintf(@text('COM-TODOS-STORY-NEW-TODOS'), @name($subject)) ?>
</data>

<data name="body">	
<ol>
	<?php foreach($object as $obj) : ?>
	<?php
	  $priority = '';
	  switch($obj->priority)
	  {
			case ComTodosDomainEntityTodo::PRIORITY_HIGHEST: $priority =  @text('COM-TODOS-TODO-PRIORITY-HIGHEST');break;
			case ComTodosDomainEntityTodo::PRIORITY_HIGH: $priority = @text('COM-TODOS-TODO-PRIORITY-HIGH');break;
			case ComTodosDomainEntityTodo::PRIORITY_NORMAL: $priority=  @text('COM-TODOS-TODO-PRIORITY-NORMAL');break;
			case ComTodosDomainEntityTodo::PRIORITY_LOW:$priority = @text('COM-TODOS-TODO-PRIORITY-LOW');break;
			default : $priority = @text('COM-TODOS-TODO-PRIORITY-LOWEST');break;
	  }
	?>	
	<li><?= @link($obj) ?> <span class="an-meta"> - (<?= @text('COM-TODOS-TODO-PRIORITY') ?>: <?=$priority?>)</span></li>
	<?php endforeach; ?>
</ol>
</data>
<?php else : ?>
<data name="title">
	<?= sprintf(@text('COM-TODOS-STORY-NEW-TODO'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">
    <h4 class="entity-title">
    	<a href="<?= @route($object->getURL()) ?>">
    		<?= $object->title ?>
    	</a>
    </h4>
    <div class="entity-body">
	    <?= @helper('text.truncate', @content(strip_tags($object->body), array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
	</div>	
</data>
<?php endif; ?>