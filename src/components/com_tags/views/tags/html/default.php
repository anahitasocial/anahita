<? defined('KOOWA') or die; ?>

<?= @helper('ui.header') ?>

<?= @infinitescroll($items, array(
  'url' => 'layout=list&sort='.$sort,
  'id' => 'an-tags'
)) ?>
