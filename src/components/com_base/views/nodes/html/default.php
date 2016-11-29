<? defined('KOOWA') or die; ?>

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
  'id' => 'an-nodes',
  'url' => $url,
  'hiddenlink' => true,
)) ?>
