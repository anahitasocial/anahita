<? defined('KOOWA') or die ?>

<?
$url = array('layout' => 'gadget_list');

if (isset($filter)) {
    $url['filter'] = $filter;
} elseif (isset($actor)) {
    $url['oid'] = $actor->id;
}
?>

<div class="an-entities" data-trigger="InfiniteScroll" data-url="<?= @route($url) ?>">
<?= @template('gadget_list') ?>
</div>
