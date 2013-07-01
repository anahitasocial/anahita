<?php defined('KOOWA') or die('Restricted access');?>

<?php if ( is_array($object) ) : ?>
<data name="title">	
	<?= sprintf(@text('COM-TODOS-STORY-TODOS-OPENED'), @name($subject), count($object), @possessive($target))?>
</data>
<data name="body">	
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
	<div>
		<?= @link($obj) ?> <span class="an-meta"> - (<?= @text('COM-TODOS-TODO-PRIORITY') ?>: <?=$priority?>)</span>			
	</div>
	<?php endforeach; ?>
</data>
<?php else : ?>
<data name="title">
	<?= sprintf(@text('COM-TODOS-STORY-TODO-OPENED'), @link($subject), @name($object),  @possessive($target))?>
</data>
<data name="body">	
	<?php
		$priority = '';
		switch($object->priority)
		{
			case ComTodosDomainEntityTodo::PRIORITY_HIGHEST: $priority =  @text('COM-TODOS-TODO-PRIORITY-HIGHEST');break;
			case ComTodosDomainEntityTodo::PRIORITY_HIGH: $priority = @text('COM-TODOS-TODO-PRIORITY-HIGH');break;
			case ComTodosDomainEntityTodo::PRIORITY_NORMAL: $priority=  @text('COM-TODOS-TODO-PRIORITY-NORMAL');break;
			case ComTodosDomainEntityTodo::PRIORITY_LOW:$priority = @text('COM-TODOS-TODO-PRIORITY-LOW');break;
			default : $priority = @text('COM-TODOS-TODO-PRIORITY-LOWEST');break;
		}
	?>	
	<div>
		<?= @link($object) ?> <span class="an-meta"> - (<?= @text('COM-TODOS-TODO-PRIORITY') ?>: <?=$priority?>)</span>			
	</div>
</data>
<?php endif; ?>