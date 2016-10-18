<? defined('KOOWA') or die; ?>

<?
//set the actor as state
if ($item->isAdministrable()) {
    @listItemView()->getState()->actor = $item;
}
?>

<? @listItemView()->layout('list') ?>
<? foreach ($items as $item) : ?>
<?= @listItemView()->item($item)?>
<? endforeach; ?>
