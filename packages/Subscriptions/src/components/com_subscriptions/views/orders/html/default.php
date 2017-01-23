<? defined('KOOWA') or die('Restricted access'); ?>

<?= @helper('ui.header'); ?>

<?= @infinitescroll($items, array(
  'id' => 'an-orders',
  'url' => array('layout' => 'list'),
  'columns' => 2
)) ?>
