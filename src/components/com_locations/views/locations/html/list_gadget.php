<?php defined('KOOWA') or die; ?>

<?php if (count($items)): ?>
<ul class="nav nav-pills nav-stacked">
<?php foreach ($items as $item): ?>
	<li>
		<a href="<?= @route($item->getURL()) ?>">
			<i class="icon-map-marker"></i> 
			<?= $item->name ?>
    </a>
	</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
