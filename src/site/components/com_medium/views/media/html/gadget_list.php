<?php defined('KOOWA') or die('Restricted access');?>	
				
<?php if(count($items)) :?>
	<?php foreach($items as $item): ?>
	<?= @listItemView()->layout('gadget_list')->item($item)->filter($filter) ?>
	<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('LIB-AN-MEDIUMS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>