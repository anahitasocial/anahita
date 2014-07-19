<?php defined('KOOWA') or die; ?>

<ul>
<?php foreach($items as $item): ?>
	<li>
		<a href="<?= @route($item->getURL()) ?>">#<?= $item->name ?></a>
	</li>
<?php endforeach; ?>
</ul>