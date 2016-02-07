<?php defined('KOOWA') or die('Restricted access');?>

<?= @helper('ui.header', array()) ?>
<?= @infinitescroll($photos, array(
  'id' => 'an-photos',
  'layout_list' => 'masonry',
  'layout_item' => 'masonry',
  'hiddenlink' => true
)) ?>
