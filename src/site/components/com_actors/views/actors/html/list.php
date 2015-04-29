<?php defined('KOOWA') or die ?>

<?php if(count($items)) :?>
	<?php @listItemView()->layout('list') ?>
	<?php foreach($items as $item ) : ?>
		<?= @listItemView()->item($item)?>
	<?php endforeach; ?>
<?php else : ?>
	<?= @message(@text('LIB-AN-PROMPT-NO-MORE-RECORDS-AVAILABLE')) ?>
<?php endif; ?>