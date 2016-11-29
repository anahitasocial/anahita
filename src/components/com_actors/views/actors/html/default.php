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

<?= @helper('ui.filterbox', @route($url)) ?>
<?= @infinitescroll($items, array(
  'id' => 'an-actors',
  'url' => $url,
  'hiddenlink' => true,
)) ?>
