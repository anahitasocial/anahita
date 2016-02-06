<?php defined('KOOWA') or die('Restricted access');?>

<?php if (count($photos)) : ?>

<?php
$url = array('layout' => 'masonry_list');

if (isset($filter)) {
    $url['filter'] = $filter;
} elseif (isset($actor)) {
    $url['oid'] = $actor->id;
}
?>

<div id="an-photos" class="an-entities" data-trigger="InfiniteScroll" data-url="<?= @route($url) ?>">
  <div class="row">
    <?= @template('masonry_list') ?>
  </div>
</div>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
