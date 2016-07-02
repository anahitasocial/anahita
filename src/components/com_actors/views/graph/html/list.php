<?php defined('KOOWA') or die; ?>

<?php
//set the actor as state
if ($item->isAdministrable()) {
    @listItemView()->getState()->actor = $item;
}
?>

<?php @listItemView()->layout('list') ?>
<?php foreach ($items as $item) : ?>
<?= @listItemView()->item($item)?>
<?php endforeach; ?>
