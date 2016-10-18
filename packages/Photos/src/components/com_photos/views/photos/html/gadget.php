<? defined('KOOWA') or die('Restricted access');?>

<? if (count($photos)) : ?>
<?
$url['layout'] = 'masonry_list';

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
<? else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
