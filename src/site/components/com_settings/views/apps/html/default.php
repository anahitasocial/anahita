<? defined('KOOWA') or die; ?>

<div class="row">
  <div class="span2">
      <?= @helper('ui.navigation', array('selected' => 'apps')) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>
      <table class="table table-striped">
          <thead>
              <th><?= @text('LIB-AN-ENTITY-NAME') ?></th>
              <th><?= @text('LIB-AN-ENTITY-PACKAGE') ?></th>
              <th></th>
          </thead>
          <tbody>
          <? foreach ($items as $item) : ?>
          <tr>
              <td>
                <a href="<?= @route('view=app&layout=edit&id='.$item->id) ?>">
                  <?= @escape($item->name) ?> <?= $item->assignable ?>
                </a>
              </td>
              <td><?= @escape($item->package) ?></td>
              <td><i class="icon icon-resize-vertical">&nbsp;</i></td>
          </tr>
          <? endforeach; ?>
          </tbody>
      </table>
  </div>
</div>
