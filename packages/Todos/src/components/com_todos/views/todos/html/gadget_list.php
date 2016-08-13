<? defined('KOOWA') or die('Restricted access');?>	

<? if (count($todos)) :?>
	<? foreach ($todos as $todo) : ?>
	<?= @view('todo')->layout('list')->todo($todo)->filter($filter) ?>
	<? endforeach; ?>
<? else: ?>
<?= @message(@text('COM-TODOS-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
