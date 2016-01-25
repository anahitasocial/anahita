<?php defined('KOOWA') or die; ?>

<?php foreach ($items as $item) : ?>
<?= @listItemView()->layout('list')->item($item) ?>
<?php endforeach; ?>
