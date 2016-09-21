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

<?= @infinitescroll($people, array(
  'id' => 'an-people',
  'url' => $url,
  'hiddenlink' => true,
)) ?>
