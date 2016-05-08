<? defined('KOOWA') or die; ?>

<? if ($sort == 'ordering') : ?>
    <? if (defined('JDEBUG') && JDEBUG) : ?>
    <script src="media/com_settings/js/orderable.js" />
    <? else: ?>
    <script src="com_settings/js/orderable.min.js" />
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
          <? foreach ($items as $item) : ?>
          <tr data-url="<?= @route($item->getURL().'&layout=edit') ?>">
              <td style="width: 100%;">
                <a href="<?= @route('view=app&layout=edit&id='.$item->id) ?>">
                  <?= @escape($item->name) ?>
                </a>
              </td>
              <td><?= @escape($item->package) ?></td>
              <td>
                <a
                  class="js-orderable-handle"
                  style="cursor: <?= ($sort == 'ordering') ? 'move' : 'not-allowed' ?>"
                >
                  <i class="icon icon-resize-vertical"></i>
                </a>
              </td>
          </tr>
          <? endforeach; ?>
          </tbody>
      </table>
  </div>
</div>
