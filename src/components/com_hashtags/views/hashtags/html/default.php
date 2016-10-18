<? defined('KOOWA') or die; ?>

<?= @helper('ui.header') ?>

<? if (count($items)): ?>
<ul class="nav nav-pills">
<? foreach ($items as $item): ?>
	<li>
		<a href="<?= @route($item->getURL()) ?>">#<?= $item->name ?></a>
	</li>
<? endforeach; ?>
</ul>
<? else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
