<? defined('KOOWA') or die; ?>

<?= @helper('ui.header') ?>
<?= @helper('ui.filterbox', @route('layout=list&sort='.$sort)) ?>
<?= @infinitescroll($locations, array(
  'url' => 'sort='.$sort,
  'start' => $start,
  'hiddenlink' => true,
)) ?>
