<?php defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<?php if(count($todolists)): ?>
	<?php foreach($todolists as $todolist) : ?>
		<?= @view('todolist')->layout('list')->todolist($todolist) ?>
	<?php endforeach; ?>
<?php else : ?>
<?= @message(@text('COM-TODOS-TODOLISTS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>

<?= @pagination($todolists, array('url'=>@route('layout=list'))) ?>