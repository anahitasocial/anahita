<?php defined('KOOWA') or die('Restricted access');?>

<?php if ( is_array($object) ) : ?>
<data name="title">	
	<?= sprintf(@text('COM-TODOS-STORY-TODOS-CLOSED'), @name($subject)) ?>
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
	<?=sprintf(@text('COM-TODOS-STORY-TODO-CLOSED'), @name($subject), @route($object->getURL()))?>
</data>
<data name="body">
    <div>
		<?= @link($object) ?> 			
	</div>
</data>
<?php endif; ?>


