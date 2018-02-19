<? defined('KOOWA') or die; ?>

<div class="row">
  <div class="span2">
      <?= @helper('ui.navigation', array('selected' => AnInflector::pluralize($view))) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>
      <?= @template('_form') ?>
  </div>
</div>
