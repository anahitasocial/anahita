<? defined('KOOWA') or die('Restricted access');?>

<?= @helper('ui.header') ?>

<?
$url['layout'] = 'list';
$tags = array('hashtag', 'location', 'mention');
foreach ($tags as $tag) {
    if (isset(${$tag})) {
        $url[$tag] = ${$tag};
    }
}
?>

<?= @infinitescroll($items, array(
  'id' => 'an-photos',
  'url' => $url,
  'hiddenlink' => true,
  'columns' => 2
)) ?>
