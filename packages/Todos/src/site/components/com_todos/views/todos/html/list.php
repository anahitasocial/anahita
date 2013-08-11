<?php defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<?php if(count($todos)): ?>
	<?php foreach($todos as $todo) : ?>
		<?= @view('todo')->layout('list')->todo($todo)->pid($pid)->filter($filter) ?>
	<?php endforeach; ?>
<?php else : ?>
<?= @message(@text('COM-TODOS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>

<?= @pagination($todos, array('url'=>@route('layout=list'))) ?>
