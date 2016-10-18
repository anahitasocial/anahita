<? defined('KOOWA') or die; ?>

<? if (count($items)): ?>
<ul class="nav nav-pills nav-stacked">
<? foreach ($items as $item): ?>
	<li>
		<a href="<?= @route($item->getURL()) ?>">
			<i class="icon-map-marker"></i>
			<?= $item->name ?>
    </a>
	</li>
<? endforeach; ?>
</ul>
<? else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
