<? defined('KOOWA') or die; ?>

<? foreach ($items as $item) : ?>
<tr data-ordering="<?= $item->ordering ?>" data-url="<?= @route($item->getURL()) ?>">
    <td style="width: 100%;">
      <a class="js-edit" href="<?= @route($item->getURL().'&layout=edit') ?>">
        <?= @escape($item->name) ?>
      </a>
    </td>
    <td><?= @escape($item->element) ?></td>
    <td><?= @escape($item->type) ?></td>
    <? $icon = (boolean) $item->enabled ? 'icon-ok' : ''; ?>
    <td><em class="icon <?= $icon ?>"></em></td>
    <td>
      <a class="js-orderable-handle" style="cursor: <?= ($sort == 'ordering') ? 'move' : 'not-allowed' ?>">
        <i class="icon icon-resize-vertical"></i>
      </a>
    </td>
</tr>
<? endforeach; ?>
