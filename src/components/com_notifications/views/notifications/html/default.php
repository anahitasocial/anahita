<? defined('KOOWA') or die ?>

<?= @helper('ui.header') ?>

<?= @infinitescroll(null, array(
  'id' => 'an-notifications',
  'url' => array('layout' => 'list'),
  'columns' => 2
)) ?>
