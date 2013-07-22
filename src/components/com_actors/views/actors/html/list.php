<?php defined('KOOWA') or die ?>
<?php 
	if ( empty($pagination_url) ) {
		$pagination_url = @route('layout=list');
	}
?>
<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= $pagination_url ?>'}" class="an-entities" id="an-entities-main">
	<?php @listItemView()->layout('list') ?>
	
	<?php if(count($items)) :?>
	<div id="an-actors" class="an-entities">
		<?php foreach($items as $item ) : ?>
			<?= @listItemView()->item($item)?>
		<?php endforeach; ?>
	</div>
	<?php else : ?>
	<?= @message(@text('LIB-AN-PROMPT-NO-MORE-RECORDS-AVAILABLE')) ?>
	<?php endif; ?>
</div>