<?php defined('KOOWA') or die ?>

<?php @listItemView()->layout('list') ?>
<?php foreach ($items as $item) : ?>
<?= @listItemView()->item($item)?>
<?php endforeach; ?>
