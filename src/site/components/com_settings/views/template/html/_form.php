<? defined('KOOWA') or die; ?>

<form action="<?= @route('view=template&alias='.$item->alias) ?>" method="post">
    <input type="hidden" name="action" value="edit" />
    
    <fieldset>
        <legend><?= @text('COM-SETTINGS-TEMPLATES-CONFIGURATIONS') ?></legend>
        <?= @helper('ui.params', array(
          'entity' => $item,
          'type' => 'template'
        )) ?>
    </fieldset>

    <div class="form-actions">
      <a href="<?= @route('view=templates') ?>" class="btn">
        <?= @text('LIB-AN-ACTION-CANCEL') ?>
      </a>
      <button type="submit" class="btn btn-primary">
        <?= @text('LIB-AN-ACTION-UPDATE') ?>
      </button>
    </div>
</form>
