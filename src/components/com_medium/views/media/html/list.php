<? defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<? if (count($items)): ?>
	<? foreach ($items as $item) : ?>
		<?= @listItemView()->layout('list')->item($item)->filter($filter) ?>
	<? endforeach; ?>
<? else : ?>
<?= @message(@text('LIB-AN-MEDIUMS-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
</div>

<?= @pagination($items, array('url' => @route('layout=list'))) ?>
