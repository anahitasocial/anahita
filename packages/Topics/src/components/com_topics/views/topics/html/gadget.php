<? defined('KOOWA') or die('Restricted access');?>

<? if (count($topics)) : ?>
<?
$url = array('layout' => 'gadget_list');

if (isset($filter)) {
    $url['filter'] = $filter;
} elseif (isset($actor)) {
    $url['oid'] = $actor->id;
}
?>

<?= @infinitescroll(null, array(
  'url' => $url,
  'topic' => 'an-topics'
)) ?>
<? else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
