<? defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<? foreach ($todos as $todo) : ?>
	<?= @view('todo')->layout('list')->todo($todo)->filter($filter) ?>
<? endforeach; ?>
<? if (count($todos) == 0): ?>
<?= @message(@text('COM-TODOS-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
</div>

<?
$url = 'layout=list';

if (!empty($sort)) {
    $url .= '&sort='.$sort;
}
?>

<?= @pagination($todos, array('url' => @route($url))); ?>
