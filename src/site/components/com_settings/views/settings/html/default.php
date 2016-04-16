<? defined('KOOWA') or die; ?>

<div class="row">
  <div class="span4">
      <?= @helper('ui.navigation') ?>
  </div>
  <div class="span8">
      <?= @helper('ui.header') ?>
      <?= @template('_settings') ?>
  </div>
</div>
