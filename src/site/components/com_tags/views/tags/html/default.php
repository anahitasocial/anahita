<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>

<?= @infinitescroll($items, array(
  'url' => 'layout=list&sort='.$sort,
  'id' => 'an-tags'
)) ?>
