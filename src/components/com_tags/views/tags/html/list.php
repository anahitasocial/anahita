<? defined('KOOWA') or die; ?>

<? $view = @listItemView()->layout('list'); ?>
<? foreach ($items as $item) : ?>
<?= $view->item($item) ?>
<? endforeach; ?>
