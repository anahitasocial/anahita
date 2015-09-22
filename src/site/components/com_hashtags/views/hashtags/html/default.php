<?php defined('KOOWA') or die; ?>

<?php if (count($items)): ?>
<ul class="nav nav-pills">
<?php foreach ($items as $item): ?>
	<li>
		<a href="<?= @route($item->getURL()) ?>">#<?= $item->name ?></a>
	</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>