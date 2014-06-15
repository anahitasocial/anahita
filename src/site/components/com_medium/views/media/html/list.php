<?php defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<?php if(count($items)): ?>
	<?php foreach($items as $item) : ?>
		<?= @listItemView()->layout('list')->item($item)->filter($filter) ?>
	<?php endforeach; ?>
<?php else : ?>
<?= @message(@text('LIB-AN-MEDIUMS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>

<?= @pagination($items, array('url'=>@route('layout=list'))) ?>
