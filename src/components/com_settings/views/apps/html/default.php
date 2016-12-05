<? defined('KOOWA') or die; ?>

<? if ($sort == 'ordering') : ?>
    <? if (defined('ANDEBUG') && ANDEBUG) : ?>
    <script src="com_settings/js/orderable.js" />
    <? else: ?>
    <script src="com_settings/js/min/orderable.min.js" />
    <? endif; ?>
<? endif; ?>

<div class="row">
  <div class="span2">
      <?= @helper('ui.navigation', array('selected' => $view)) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>

      <table class="table table-hover">
          <thead>
              <th><?= @helper('ui.sorting', array('field' => 'name')) ?></th>
              <th><?= @text('LIB-AN-ENTITY-PACKAGE') ?></th>
              <th><?= @helper('ui.sorting', array('field' => 'ordering')) ?></th>
          </thead>
          <tbody data-behavior="orderable">
          <?= @template('list') ?>
          </tbody>
      </table>
  </div>
</div>
