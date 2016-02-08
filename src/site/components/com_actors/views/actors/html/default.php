<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>
<?= @helper('ui.filterbox', @route('layout=list')) ?>
<?= @infinitescroll($items, array(
  'id' => 'an-actors',
  'hiddenlink' => true,
)) ?>
