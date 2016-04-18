<? defined('KOOWA') or die; ?>

<div class="row">
  <div class="span2">
      <?= @helper('ui.navigation', array('selected' => $view)) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>
      <table class="table table-striped">
          <thead>
              <th><?= @text('LIB-AN-ENTITY-NAME') ?></th>
              <th></th>
          </thead>
          <tbody>
          <? foreach ($items as $item) : ?>
          <tr>
              <td>
                <a href="<?= @route('view=plugin&layout=edit&id='.$item->id) ?>">
                  <?= @escape($item->name) ?> <?= $item->assignable ?>
                </a>
              </td>
              <td><i class="icon icon-resize-vertical">&nbsp;</i></td>
          </tr>
          <? endforeach; ?>
          </tbody>
      </table>
  </div>
</div>
