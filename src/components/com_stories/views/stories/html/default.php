<? defined('KOOWA') or die ?>

<?
$url = array('layout' => 'list');

if (isset($filter)) {
    $url['filter'] = $filter;
} elseif (isset($actor)) {
    $url['oid'] = $actor->id;
}
?>

<?= @infinitescroll($stories, array(
  'url' => $url,
  'id' => 'an-stories',
  'columns' => 1
)) ?>
