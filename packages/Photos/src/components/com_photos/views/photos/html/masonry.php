<? defined('KOOWA') or die('Restricted access');?>

<?= @helper('ui.header') ?>

<?
$url['layout'] = 'masonry_list';
$tags = array('hashtag', 'location', 'mention');
foreach ($tags as $tag) {
    if (isset(${$tag})) {
        $url[$tag] = ${$tag};
    }
}
?>

<?= @infinitescroll($photos, array(
  'id' => 'an-photos',
  'url' => $url,
  'layout_list' => 'masonry',
  'layout_item' => 'masonry',
  'hiddenlink' => true,
  'columns' => 2
)) ?>
