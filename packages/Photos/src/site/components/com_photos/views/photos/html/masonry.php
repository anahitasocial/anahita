<?php defined('KOOWA') or die('Restricted access');?>

<?= @helper('ui.header', array()) ?>
<?= @infinitescroll($photos, array(
  'id' => 'an-photos',
  'url' => 'layout=masonry_list',
  'layout_list' => 'masonry',
  'layout_item' => 'masonry',
  'hiddenlink' => true,
  'columns' => 3
)) ?>
