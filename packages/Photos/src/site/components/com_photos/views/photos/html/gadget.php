<?php defined('KOOWA') or die('Restricted access');?>

<?php if (count($photos)) : ?>
<?php
$url['layout'] = 'gadget_list';

if (isset($filter)) {
    $url['filter'] = $filter;
} elseif (isset($actor)) {
    $url['oid'] = $actor->id;
}
?>

<?= @infinitescroll(null, array(
  'layout_item' => 'masonry',
  'url' => $url,
  'id' => 'an-photos'
)) ?>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
