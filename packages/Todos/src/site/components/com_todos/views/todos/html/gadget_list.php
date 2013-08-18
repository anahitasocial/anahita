<?php defined('KOOWA') or die('Restricted access');?>	
	
<?php if(count($todos)) :?>
	<?php foreach( $todos as $todo) : ?>
	<?= @view('todo')->layout('list')->todo($todo)->filter($filter) ?>
	<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('COM-TODOS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>