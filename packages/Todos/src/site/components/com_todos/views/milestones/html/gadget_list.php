<?php defined('KOOWA') or die('Restricted access');?>	
	
<?php $days_left = 1; ?>
	
<?php if(count($milestones)) :?>
	<?php foreach( $milestones as $milestone) : ?>
	<?= @view('milestone')->layout('list')->milestone($milestone) ?>
	<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('COM-TODOS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>