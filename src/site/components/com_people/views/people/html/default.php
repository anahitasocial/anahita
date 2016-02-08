<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>

<?= @infinitescroll($people, array(
  'id' => 'an-people',
  'hiddenlink' => true,
)) ?>
