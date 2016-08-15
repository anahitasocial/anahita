<? defined('KOOWA') or die ?>

<? @listItemView()->layout('list') ?>
<? foreach ($items as $item) : ?>
<?= @listItemView()->item($item)?>
<? endforeach; ?>
