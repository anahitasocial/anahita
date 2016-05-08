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
      <?= @helper('ui.navigation', array('selected' => 'apps')) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>
      <?= @helper('ui.sorting') ?>

      <table class="table table-hover">
          <thead>
              <th><?= @text('LIB-AN-ENTITY-NAME') ?></th>
              <th><?= @text('LIB-AN-ENTITY-PACKAGE') ?></th>
              <? if ($sort == 'ordering') : ?>
              <th><?= @text('LIB-AN-ENTITY-ORDERING') ?></th>
              <? endif; ?>
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
              <? if ($sort == 'ordering') : ?>
              <td>
                <a class="js-orderable-handle" style="cursor: move">
                  <i class="icon icon-resize-vertical"></i>
                </a>
              </td>
              <? endif; ?>
          </tr>
          <? endforeach; ?>
          </tbody>
      </table>
  </div>
</div>
