<?php defined('KOOWA') or die; ?>

<div class="an-entities" id="an-entities-main">
<?php if(!empty($keywords)): ?>
	<?php if(count($items)) :?>
		<?php foreach($items as $item ) : ?>
			<?= @view('search')->layout('list')->item($item)->keywords($keywords)?>
		<?php endforeach; ?>
	<?php else : ?>
		<?= @message(@text('LIB-AN-PROMPT-NO-MORE-RECORDS-AVAILABLE')) ?>
	<?php endif; ?>
<?php endif; ?>
</div>

<?php if(!empty($keywords)): ?>
<?= @pagination($items, array('url'=>@route('layout=list&term='.$term))) ?>
<?php endif; ?>