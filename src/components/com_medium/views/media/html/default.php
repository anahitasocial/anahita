<? defined('KOOWA') or die('Restricted access'); ?>

<?= @helper('ui.header') ?>

<?
$url['layout'] = 'list';
$tags = array('hashtag', 'location', 'mention');
foreach ($tags as $tag) {
    if (isset(${$tag})) {
        $url[$tag] = ${$tag};
    }
}

if (isset($filter)) {
    $url['filter'] = $filter;
} elseif (isset($actor)) {
    $url['oid'] = $actor->id;
}

if (isset($sort)) {
    $url['sort'] = $sort;
}
?>

<?= @infinitescroll($items, array(
  'id' => 'an-media',
  'url' => $url,
  'hiddenlink' => true
)) ?>
