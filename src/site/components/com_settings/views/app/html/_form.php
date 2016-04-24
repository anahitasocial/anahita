<? defined('KOOWA') or die; ?>

<form action="<?= @route($item->getURL()) ?>" method="post" class="an-entity">

  <fieldset>
      <legend><?= @text('COM-SETTINGS-APP-DETAILS') ?></legend>
      <dl>
        <dt><?= @text('ID') ?></dt>
        <dd><?= @escape($item->id) ?></dd>
        <dt><?= @text('LIB-AN-ENTITY-NAME') ?></dt>
        <dd><?= @escape($item->name) ?></dd>
        <dt><?= @text('LIB-AN-ENTITY-PACKAGE') ?></dt>
        <dd><?= @escape($item->package) ?></dd>
      </dl>
  </fieldset>

  <h3><?= @text('COM-SETTINGS-APP-CONFIGURATIONS') ?></h3>

  <fieldset>
      <?= @helper('ui.params', array(
        'entity' => $item,
        'type' => 'component'
      )) ?>
  </fieldset>

  <div class="form-actions">
    <a href="<?= @route('view=apps') ?>" class="btn">
      <?= @text('LIB-AN-ACTION-CANCEL') ?>
    </a>
    <button type="submit" class="btn btn-primary">
      <?= @text('LIB-AN-ACTION-UPDATE') ?>
    </button>
  </div>
</form>
