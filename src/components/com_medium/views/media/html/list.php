<? defined('KOOWA') or die('Restricted access');?>

<? if (count($items)) :?>
	<? foreach ($items as $item): ?>
	<?= @listItemView()->layout('list')->item($item)->filter($filter) ?>
	<? endforeach; ?>
<? else: ?>
<?= @message(@text('LIB-AN-MEDIUMS-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
