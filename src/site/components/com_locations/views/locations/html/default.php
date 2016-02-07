<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>
<?= @helper('ui.filterbox', @route('layout=list')) ?>

<?= @infinitescroll($locations, array(
  'url' => 'sort='.$sort
)) ?>
