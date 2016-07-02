<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header') ?>
<?= @helper('ui.filterbox', @route('layout=list')) ?>
<?= @infinitescroll($items, array(
  'id' => 'an-actors',
  'hiddenlink' => true,
)) ?>
