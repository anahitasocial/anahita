<?php defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<?php foreach($todos as $todo) : ?>
	<?= @view('todo')->layout('list')->todo($todo)->filter($filter) ?>
<?php endforeach; ?>
<?php if(count($todos) == 0): ?>
<?= @message(@text('COM-TODOS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>

<?php 
$url = 'layout=list';

if(!empty($sort))
{
	$url .= '&sort='.$sort;
}
?>

<?= @pagination($todos, array('url'=>@route($url))); ?>
