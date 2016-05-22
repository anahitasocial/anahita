<? defined('KOOWA') or die; ?>

<div class="row">
  <div class="span2">
      <?= @helper('ui.navigation', array('selected' => 'actors')) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>

      <div class"an-entities">
      <? foreach ($items as $item) : ?>
      <div class="an-entity">
          <h4 class="entity-title"><?= ucfirst($item->identifier->package) ?></h4>
      </div>
      <? endforeach; ?>
      </div>
  </div>
</div>
