<?php defined('KOOWA') or die; ?>

<?php $view = @listItemView()->layout('list'); ?>
<?php foreach ($items as $item) : ?>
<?= $view->item($item) ?>
<?php endforeach; ?>
