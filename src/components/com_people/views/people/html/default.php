<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header') ?>

<?= @infinitescroll($people, array(
  'id' => 'an-people',
  'hiddenlink' => true,
)) ?>
