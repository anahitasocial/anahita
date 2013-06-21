<?php defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<?php if(count($todos)): ?>
	<?php foreach($todos as $todo) : ?>
		<?php if($todo->getRowData('search_result_preview')): ?>
		<?= @view('todo')->layout('list_search_result')->todo($todo)->pid($pid)->filter($filter)->keyword($q) ?>
		<?php else : ?>
		<?= @view('todo')->layout('list')->todo($todo)->pid($pid)->filter($filter) ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else : ?>
<?= @message(@text('COM-TODOS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>

<?= @pagination($todos, array('url'=>@route('layout=list'))) ?>
