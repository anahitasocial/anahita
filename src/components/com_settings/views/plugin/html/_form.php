<? defined('KOOWA') or die; ?>

<form action="<?= @route($item->getURL()) ?>" method="post" class="an-entity">

  <fieldset>
      <legend><?= @text('COM-SETTINGS-PLUGIN-DETAILS') ?></legend>
      <dl>
        <dt><?= @text('ID') ?></dt>
        <dd><?= @escape($item->id) ?></dd>
        <dt><?= @text('LIB-AN-ENTITY-NAME') ?></dt>
        <dd><?= @escape($item->name) ?></dd>
      </dl>
  </fieldset>

  <h3><?= @text('COM-SETTINGS-APP-CONFIGURATIONS') ?></h3>

  <fieldset>
      <?= @helper('ui.params', array(
        'entity' => $item,
        'type' => 'plugin'
      )) ?>
  </fieldset>

  <div class="form-actions">
    <a href="<?= @route('view=plugins') ?>" class="btn">
      <?= @text('LIB-AN-ACTION-CANCEL') ?>
    </a>

    <button type="submit" class="btn btn-primary">
      <?= @text('LIB-AN-ACTION-UPDATE') ?>
    </button>
  </div>
</form>
